<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class ScalarType implements Type
{
    public function accepts(Type $type): bool
    {
        return $type instanceof StringType
            || $type instanceof IntegerType
            || $type instanceof FloatType
            || $type instanceof BooleanType;
    }

    public function __toString(): string
    {
        return 'scalar';
    }
}
