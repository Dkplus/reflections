<?php
namespace Dkplus\Reflections\Type;

final class FloatType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }

    public function __toString(): string
    {
        return 'float';
    }
}
