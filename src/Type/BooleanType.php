<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class BooleanType implements Type
{
    public function accepts(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'accepts'], $type->innerTypes()));
        }
        return $type instanceof self;
    }

    public function __toString(): string
    {
        return 'bool';
    }
}
