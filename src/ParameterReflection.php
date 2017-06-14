<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\Type\Type;
use ReflectionParameter;

class ParameterReflection
{
    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var int */
    private $position;

    /** @var boolean */
    private $omittable;

    /** @var bool */
    private $variadic;

    /** @var string */
    private $description;

    public function __construct(
        string $name,
        Type $type,
        int $position,
        bool $omittable,
        bool $variadic,
        string $description
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->position = $position;
        $this->omittable = $omittable;
        $this->variadic = $variadic;
        $this->description = $description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function description(): string
    {
        return $this->description;
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
