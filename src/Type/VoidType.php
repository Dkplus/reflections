<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class VoidType implements Type
{
    public function __toString(): string
    {
        return 'void';
    }

    public function accepts(Type $type): bool
    {
        return false;
    }
}
