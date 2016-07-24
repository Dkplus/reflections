<?php
namespace Dkplus\Reflections\Type;

final class ResourceType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }

    public function __toString(): String
    {
        return 'resource';
    }
}
