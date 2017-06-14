<?php
namespace Dkplus\Reflection;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflection\ReflectionParameter;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflection\Scanner\AnnotationScanner;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use phpDocumentor\Reflection\Types\Mixed;

class BetterReflectionClassReflection
{
    /** @var ReflectionClass */
    private $reflectionClass;

    /** @var AnnotationScanner */
    private $annotations;

    /** @var array */
    private $imports;

    /** @var TypeFactory */
    private $typeFactory;

    /** @var Reflector */
    private $reflector;

    /**
     * @internal
     */
    public function __construct(
        ReflectionClass $reflectionClass,
        AnnotationScanner $annotations,
        Reflector $reflector,
        TypeFactory $typeFactory,
        array $imports
    ) {
        $this->reflectionClass = $reflectionClass;
        $this->annotations = $annotations;
        $this->imports = $imports;
        $this->reflector = $reflector;
        $this->typeFactory = $typeFactory;
    }

    public function name(): string
    {
        return $this->reflectionClass->getName();
    }

    public function isFinal(): bool
    {
        return $this->reflectionClass->isFinal();
    }

    public function isAbstract(): bool
    {
        return $this->reflectionClass->isAbstract();
    }

    public function isInvokable(): bool
    {
        return $this->methods()->contains('__invoke');
    }

    public function isSubclassOf(string $className): bool
    {
        return $this->reflectionClass->isSubclassOf($className);
    }

    public function isCloneable(): bool
    {
        return $this->reflectionClass->isCloneable();
    }

    public function implementsInterface(string $className): bool
    {
        return $this->reflectionClass->implementsInterface($className);
    }

    public function annotations(): Annotations
    {
        return $this->annotations->scanForAnnotations(
            $this->reflectionClass->getDocComment(),
            $this->reflectionClass->getFileName(),
            $this->imports
        );
    }

    public function fileName(): string
    {
        return $this->reflectionClass->getFileName();
    }

    public function properties(): Properties
    {
        return new Properties($this->name(), array_map(function (ReflectionProperty $property) {
            return new PropertyReflection(
                $property,
                $this->typeFactory->create($this->reflector, new Mixed(), $property->getDocBlockTypeStrings(), false),
                $this->annotations->scanForAnnotations($property->getDocComment(), $this->fileName(), $this->imports)
            );
        }, $this->reflectionClass->getProperties()));
    }

    public function methods(): Methods
    {
        return new Methods($this->name(), array_map(function (ReflectionMethod $method) {
            $returnType = $this->typeFactory->create(
                $this->reflector,
                $method->getReturnType() ? $method->getReturnType()->getTypeObject() : new Mixed(),
                $method->getDocBlockReturnTypes(),
                $method->getReturnType() ? $method->getReturnType()->allowsNull() : false
            );
            $parameters = array_map(function (ReflectionParameter $parameter) {
                return new ParameterReflection(
                    $parameter,
                    $this->typeFactory->create(
                        $this->reflector,
                        $parameter->getTypeHint(),
                        $parameter->getDocBlockTypeStrings(),
                        $parameter->allowsNull()
                    ),
                    $parameter->getPosition(),
                    $parameter->isOptional()
                );
            }, $method->getParameters());
            return new MethodReflection(
                $method,
                $this->annotations->scanForAnnotations($method->getDocComment(), $this->fileName(), $this->imports),
                new Parameters($this->name() . '::' . $method->getName(), $parameters),
                $returnType
            );
        }, $this->reflectionClass->getMethods()));
    }
}
