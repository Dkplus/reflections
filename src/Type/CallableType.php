<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class CallableType implements Type
{
    public function accepts(Type $type): bool
    {
        if ($type instanceof self) {
            return true;
        }
        if ($type instanceof ClassType) {
            return $type->isInvokable();
        }
        return false;
    }

    public function __toString(): string
    {
        return 'callable';
    }
}
