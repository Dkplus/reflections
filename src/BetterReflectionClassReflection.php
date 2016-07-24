<?php
namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Scanner\AnnotationScanner;

/**
 * @api
 */
class BetterReflectionClassReflection implements ClassReflection
{
    /** @var ReflectionClass */
    private $reflectionClass;

    /** @var AnnotationScanner */
    private $annotations;

    /** @var array */
    private $imports;

    /**
     * @internal
     */
    public function __construct(
        ReflectionClass $reflectionClass,
        AnnotationScanner $annotations,
        array $imports
    ) {
        $this->reflectionClass = $reflectionClass;
        $this->annotations = $annotations;
        $this->imports = $imports;
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
            return new PropertyReflection($property, $this->annotations, $this->imports);
        }, $this->reflectionClass->getProperties()));
    }

    function methods(): Methods
    {
        return new Methods($this->name(), array_map(function (ReflectionMethod $method) {
            return new MethodReflection($method, $this->annotations, $this->imports);
        }, $this->reflectionClass->getMethods()));
    }
}
