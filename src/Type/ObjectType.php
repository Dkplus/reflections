<?php
namespace Dkplus\Reflections\Type;

class ObjectType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof ObjectType
            || $type instanceof ClassType;
    }

    public function __toString(): String
    {
        return 'object';
    }
}
