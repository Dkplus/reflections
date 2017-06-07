<?php
namespace Dkplus\Reflection;

use BetterReflection\Reflection\ReflectionParameter;
use Dkplus\Reflection\Type\Type;

class Parameter
{
    /** @var ReflectionParameter */
    private $reflection;

    /** @var Type */
    private $type;

    /** @var int */
    private $position;

    /** @var boolean */
    private $omittable;

    public function __construct(ReflectionParameter $parameter, Type $type, int $position, $omittable)
    {
        $this->reflection = $parameter;
        $this->type = $type;
        $this->position = $position;
        $this->omittable = $omittable;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function allows(Type $type): bool
    {
        return $this->type->accepts($type);
    }

    public function canBeOmitted(): bool
    {
        return $this->omittable;
    }
}
