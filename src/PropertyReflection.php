<?php
namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Scanner\AnnotationScanner;

/**
 * @api
 */
class PropertyReflection
{
    /** @var ReflectionProperty */
    private $reflection;

    /** @var AnnotationScanner */
    private $annotations;

    /** @var array */
    private $imports;

    /**
     * @internal
     */
    public function __construct(ReflectionProperty $reflection, AnnotationScanner $annotations, array $imports)
    {
        $this->reflection = $reflection;
        $this->annotations = $annotations;
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

    public function types(): array
    {
        return $this->reflection->getDocBlockTypeStrings();
    }

    public function mainType(): string
    {
        $types = $this->types();
        return current($types);
    }

    public function annotations(): Annotations
    {
        return $this->annotations->scanForAnnotations(
            $this->reflection->getDocComment(),
            $this->reflection->getDeclaringClass()->getFileName(),
            $this->imports
        );
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }
}
