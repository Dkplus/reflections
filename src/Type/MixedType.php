<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class MixedType implements Type
{
    public function accepts(Type $type): bool
    {
        return ! $type instanceof VoidType;
    }

    public function __toString(): string
    {
        return 'mixed';
    }
}
