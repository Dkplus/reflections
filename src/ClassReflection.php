<?php
namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Scanner\AnnotationScanner;

/**
 * @api
 */
final class ClassReflection
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
}
