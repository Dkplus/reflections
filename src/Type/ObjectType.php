<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

class ObjectType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof ObjectType
            || $type instanceof ClassType;
    }

    public function __toString(): string
    {
        return 'object';
    }
}
