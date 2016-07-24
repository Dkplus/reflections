<?php
namespace Dkplus\Reflections\Type;

final class NullType implements Type
{
    public function __toString(): string
    {
        return 'null';
    }

    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }
}
