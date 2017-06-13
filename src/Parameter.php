<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\Type\Type;
use ReflectionParameter;

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

    /** @var bool */
    private $variadic;

    public function __construct(
        ReflectionParameter $parameter,
        Type $type,
        int $position,
        bool $omittable,
        bool $variadic
    ) {
        $this->reflection = $parameter;
        $this->type = $type;
        $this->position = $position;
        $this->omittable = $omittable;
        $this->variadic = $variadic;
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

    public function isVariadic(): bool
    {
        return $this->variadic;
    }

    public function canBeOmitted(): bool
    {
        return $this->omittable;
    }
}
