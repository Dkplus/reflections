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
use Dkplus\Reflection\Parameters;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\Factory\TypeConverter;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use Dkplus\Reflection\Type\Factory\TypeNormalizer;
use Dkplus\Reflection\Type\MixedType;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Mixed;
use ReflectionClass;
use ReflectionException;
use function array_filter;
use function array_map;

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
            $typeHint = new Mixed();
            if ($eachMethod->getReturnType()) {
                if ($eachMethod->getReturnType()->isBuiltin()) {
                    $typeHint = (new TypeResolver())->resolve((string) $eachMethod->getReturnType(), $context);
                } else {
                    $typeHint = (new TypeResolver())->resolve('\\' . $eachMethod->getReturnType(), $context);
                }
            }
            $fqsen = new Fqsen('\\' . $class->getName() . '::' . $eachMethod->getName() . '()');
            $returnType = $this->typeFactory->create($typeHint, $docType, $fqsen);

            $methods[] = new MethodReflection(
                $eachMethod,
                new Annotations(),
                new Parameters($eachMethod->getName()),
                $returnType
            );
        }
        return new Methods($class->getName(), ...$methods);
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
