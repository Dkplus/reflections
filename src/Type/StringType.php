<?php
namespace Dkplus\Reflections\Type;

final class StringType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }

    public function __toString(): String
    {
        return 'string';
    }
}
