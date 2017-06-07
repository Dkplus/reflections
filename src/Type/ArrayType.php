<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class ArrayType implements DecoratingType
{
    /** @var Type */
    private $type;

    public function __construct(Type $type = null)
    {
        $this->type = $type ?: new MixedType();
    }

    public function decoratedType(): Type
    {
        return $this->type;
    }

    public function accepts(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'accepts'], $type->decoratedTypes()));
        }
        if ($type instanceof self) {
            return $this->decoratedType()->accepts($type->decoratedType());
        }
        return false;
    }

    public function __toString(): string
    {
        return $this->type instanceof MixedType
            ? 'array'
            : "array<{$this->type}>";
    }
}
