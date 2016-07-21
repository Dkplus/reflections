<?php

namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionMethod;
use Dkplus\Reflections\Scanner\AnnotationScanner;

/**
 * @api
 */
class MethodReflection
{
    /** @var ReflectionMethod */
    private $reflection;

    /** @var AnnotationScanner */
    private $annotationScanner;

    /** @var array */
    private $imports;

    /**
     * @internal
     */
    public function __construct(ReflectionMethod $reflection, AnnotationScanner $annotationScanner, array $imports)
    {
        $this->reflection = $reflection;
        $this->annotationScanner = $annotationScanner;
        $this->imports = $imports;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function returnType()
    {
        return $this->reflection->getReturnType()
            ? (string) $this->reflection->getReturnType()->getTypeObject()
            : null;
    }

    public function isGetterOf(string $property): bool
    {
        return $this->countParameters() === 0
            && preg_match("/^return [^;]*\\\$this->{$property}[^;]*;$/", $this->reflection->getBodyCode());
    }

    public function countParameters(): int
    {
        return $this->reflection->getNumberOfParameters();
    }
}
