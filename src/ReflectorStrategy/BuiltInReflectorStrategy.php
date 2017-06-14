<?php
declare(strict_types=1);

namespace Dkplus\Reflection\ReflectorStrategy;

use Dkplus\Reflection\Annotation\AnnotationFactory;
use Dkplus\Reflection\Annotation\AnnotationReflector;
use Dkplus\Reflection\Annotation\HoaParser;
use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\MethodReflection;
use Dkplus\Reflection\Methods;
use Dkplus\Reflection\ParameterReflection;
use Dkplus\Reflection\Parameters;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\Factory\TypeConverter;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use Dkplus\Reflection\Type\Factory\TypeNormalizer;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Mixed;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionType;
use function array_filter;
use function array_map;
use function var_dump;

final class BuiltInReflectorStrategy implements ReflectorStrategy
{
    /** @var AnnotationReflector */
    private $annotationReflector;

    /** @var ContextFactory */
    private $contextFactory;

    /** @var TypeFactory */
    private $typeFactory;

    public function __construct()
    {
        $this->annotationReflector = new AnnotationReflector(
            new HoaParser(),
            new AnnotationFactory($this, new FqsenResolver())
        );
        $this->typeFactory = new TypeFactory(new TypeConverter(new BuiltInClassReflector()), new TypeNormalizer());
        $this->contextFactory = new ContextFactory();
    }

    public function reflectClass(string $className): ClassReflection
    {
        try {
            $class = new ReflectionClass($className);
        } catch (ReflectionException $error) {
            throw new ClassNotFound("Class $className could not be reflected");
        }

        $parents = $class->getParentClass() ? [$class->getParentClass()->name] : [];
        $interfaces = $class->getInterfaceNames();
        $traits = $class->getTraitNames();
        if ($class->isInterface()) {
            $parents = $this->filterNonImmediateInterfaces($class->getInterfaceNames(), $class->getInterfaceNames());
            $interfaces = [];
        }

        $parents = new Classes(...array_map([$this, 'reflectClass'], $parents));

        $possibleParents = $interfaces;
        if ($parents->count() > 0) {
            $possibleParents[] = $parents->first()->name();
        }
        $interfaces = $this->filterNonImmediateInterfaces($interfaces, $possibleParents);
        $interfaces = new Classes(...array_map([$this, 'reflectClass'], $interfaces));

        $traits = new Classes(...array_map([$this, 'reflectClass'], $traits));

        $context = $this->contextFactory->createFromReflector($class);
        $annotations = new Annotations();
        if ($class->getDocComment()) {
            $annotations = $this->annotationReflector->reflectDocBlock($class->getDocComment(), $context);
        }

        return new ClassReflection(
            $class,
            $annotations,
            $parents,
            $interfaces,
            $traits,
            new Properties($className),
            $this->reflectMethods($class, $context)
        );
    }

    private function reflectMethods(ReflectionClass $class, Context $context): Methods
    {
        $methods = [];
        foreach ($class->getMethods() as $eachMethod) {
            $annotations = new Annotations();
            if ($eachMethod->getDocComment()) {
                $annotations = $this->annotationReflector->reflectDocBlock($eachMethod->getDocComment(), $context);
            }

            // return type
            $docType = new Mixed();
            if ($annotations->contains('return')) {
                $docType = $annotations->oneNamed('return')->attributes()['type'];
            }
            $typeHint = $this->phpDocTypeFromReflectionType($eachMethod->getReturnType(), $context);
            $fqsen = new Fqsen('\\' . $class->getName() . '::' . $eachMethod->getName() . '()');
            $returnType = $this->typeFactory->create($typeHint, $docType, $fqsen);

            $methods[] = new MethodReflection(
                $eachMethod,
                $annotations,
                $this->reflectParameters($eachMethod, $annotations, $context, $fqsen),
                $returnType
            );
        }
        return new Methods($class->getName(), ...$methods);
    }

    private function reflectParameters(
        ReflectionMethod $eachMethod,
        Annotations $annotations,
        Context $context,
        Fqsen $fqsen
    ): Parameters {
        $parameters = [];
        $phpDocParamsByName = [];
        foreach ($annotations->named('param') as $eachParameter) {
            $attributes = $eachParameter->attributes();
            $phpDocParamsByName[substr(str_replace('...', '', $attributes['name']), 1)] = $attributes;
        }
        foreach ($eachMethod->getParameters() as $eachParameter) {
            $type = $this->typeFactory->create(
                $this->phpDocTypeFromReflectionType(
                    $eachParameter->getType(),
                    $context,
                    $eachParameter->allowsNull(),
                    $eachParameter->isVariadic()
                ),
                $phpDocParamsByName[$eachParameter->getName()]['type'] ?? new Mixed(),
                $fqsen
            );
            $handledTypes[] = $eachParameter->getName();
            $parameters[] = new ParameterReflection(
                $eachParameter->getName(),
                $type,
                $eachParameter->getPosition(),
                $eachParameter->isOptional(),
                $eachParameter->isVariadic(),
                $phpDocParamsByName[$eachParameter->getName()]['description'] ?? ''
            );
            if (isset($phpDocParamsByName[$eachParameter->getName()])) {
                unset($phpDocParamsByName[$eachParameter->getName()]);
            }
        }
        foreach ($phpDocParamsByName as $eachName => $attributes) {
            $parameters[] = new ParameterReflection(
                $eachName,
                $this->typeFactory->create(new Mixed(), $attributes['type'], $fqsen),
                count($parameters),
                false,
                false,
                $attributes['description']
            );
        }
        return new Parameters($eachMethod->getName(), ...$parameters);
    }

    private function phpDocTypeFromReflectionType(
        ?ReflectionType $type,
        Context $context,
        bool $allowsNull = false,
        bool $variadic = false
    ) {
        if (! $type) {
            return new Mixed();
        }
        $stringType = (string) $type;
        if ($variadic) {
            $stringType .= '[]';
        }
        if ($allowsNull) {
            $stringType .= '|null';
        }
        if ($type->isBuiltin()) {
            return (new TypeResolver())->resolve($stringType, $context);
        }
        return (new TypeResolver())->resolve('\\' . $stringType, $context);
    }

    private function filterNonImmediateInterfaces(array $interfaces, array $possibleParents): array
    {
        /* @var $reflectionsOfParents ReflectionClass[] */
        $reflectionsOfParents = array_map(function (string $className) {
            return new ReflectionClass($className);
        }, $possibleParents);

        return array_filter($interfaces, function (string $interface) use ($reflectionsOfParents) {
            foreach ($reflectionsOfParents as $each) {
                if ($each->name !== $interface && $each->implementsInterface($interface)) {
                    return false;
                }
            }
            return true;
        });
    }
}
