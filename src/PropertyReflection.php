<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\Type\Type;
use ReflectionProperty;

class PropertyReflection
{
    /** @var ReflectionProperty */
    private $reflection;

    /** @var Annotations */
    private $annotations;

    /** @var Type */
    private $type;

    /** @internal */
    public function __construct(ReflectionProperty $reflection, Type $type, Annotations $annotations)
    {
        $this->reflection = $reflection;
        $this->type = $type;
        $this->annotations = $annotations;
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

    public function type(): Type
    {
        return $this->type;
    }

    public function allows(Type $type): bool
    {
        return $this->type->accepts($type);
    }

    public function annotations(): Annotations
    {
        return $this->annotations;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }
}
