<?php
namespace Dkplus\Reflections\Type;

final class VoidType implements Type
{
    public function __toString(): string
    {
        return 'void';
    }

    public function allows(Type $type): bool
    {
        return false;
    }
}
