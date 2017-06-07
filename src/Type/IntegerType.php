<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class IntegerType implements Type
{
    public function accepts(Type $type): bool
    {
        return $type instanceof self;
    }

    public function __toString(): string
    {
        return 'int';
    }
}
