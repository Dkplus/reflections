<?php
namespace Dkplus\Reflections\Type;

final class BooleanType implements Type
{
    public function allows(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'allows'], $type->decoratedTypes()));
        }
        return $type instanceof self
            || $type instanceof TrueType
            || $type instanceof FalseType;
    }

    public function __toString(): String
    {
        return 'bool';
    }
}
