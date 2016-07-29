<?php
namespace Dkplus\Reflections\Type;

final class MixedType implements Type
{
    public function allows(Type $type): bool
    {
        return ! $type instanceof VoidType;
    }

    public function __toString(): string
    {
        return 'mixed';
    }
}
