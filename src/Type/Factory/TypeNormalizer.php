<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ScalarType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Traversable;
use const SORT_REGULAR;
use function array_diff;
use function array_filter;
use function array_unique;
use function in_array;

class TypeNormalizer
{
    public function normalize(Type $type): Type
    {
        $isNullable = false;
        while ($type instanceof NullableType) {
            $isNullable = true;
            $type = $type->innerType();
        }
        $type = $this->flattenComposed($type);
        $type = $this->normalizeScalar($type);
        $type = $this->unifyComposed($type);
        $type = $this->arrayToIterateable($type);
        $type = $this->normalizeArrayAndIterableAndCollection($type);
        $type = $this->moveNullToNullable($type);
        if ($isNullable && ! $type instanceof NullableType && ! $type instanceof NullType) {
            $type = new NullableType($type);
        }
        return $type;
    }

    private function arrayToIterateable(Type $type): Type
    {
        if ($type instanceof ArrayType && ! $type->innerType() instanceof MixedType) {
            return new IterableType($type->innerType());
        }
        return $type;
    }

    private function flattenComposed(Type $type): Type
    {
        if (! $type instanceof ComposedType) {
            return $type;
        }
        $isNullable = false;
        $types = $type->innerTypes();
        $types = array_merge([], ...array_map(function (Type $type) use (&$isNullable) {
            $type = $this->normalize($type);
            if ($type instanceof NullableType) {
                $isNullable = true;
                $type = $type->innerType();
            }
            if ($type instanceof ComposedType) {
                return $type->innerTypes();
            }
            return [$type];
        }, $types));
        if ($isNullable) {
            $types[] = new NullType();
        }
        return new ComposedType(...$types);
    }

    private function unifyComposed(Type $type): Type
    {
        if (! $type instanceof ComposedType) {
            return $type;
        }

        $types = $type->innerTypes();
        $types = array_unique($types, SORT_REGULAR);
        if (count($types) > 1) {
            return new ComposedType(...$types);
        }
        return current($types);
    }

    private function normalizeScalar(Type $type): Type
    {
        if (! $type instanceof ComposedType) {
            return $type;
        }
        $types = $type->innerTypes();
        if (! in_array(new ScalarType(), $types)
            && (! in_array(new IntegerType(), $types)
                || ! in_array(new FloatType(), $types)
                || ! in_array(new StringType(), $types)
                || ! in_array(new BooleanType(), $types))
        ) {
            return $type;
        }
        $otherTypes = $this->diff(
            $types,
            [new IntegerType(), new FloatType(), new StringType(), new BooleanType()]
        );
        $scalar = new ScalarType();
        if (count($otherTypes) > 0) {
            return new ComposedType($scalar, ...$otherTypes);
        }
        return $scalar;
    }

    private function moveNullToNullable(Type $type): Type
    {
        if (! $type instanceof ComposedType) {
            return $type;
        }
        $types = $type->innerTypes();
        if (! in_array(new NullType(), $types)) {
            return $type;
        }
        $nonNullTypes = $this->diff($types, [new NullType()]);
        if (count($nonNullTypes) === 1) {
            return new NullableType(...$nonNullTypes);
        }
        return new NullableType(new ComposedType(...$nonNullTypes));
    }

    private function normalizeArrayAndIterableAndCollection(Type $type): Type
    {
        if (! $type instanceof ComposedType) {
            return $type;
        }
        $types = $type->innerTypes();
        $createCollection = count(array_filter($types, function (Type $type) {
            return $type instanceof ClassType
                && $type->implementsOrIsSubClassOf(Traversable::class);
        })) > 0;
        $createArray = count(array_filter($types, function (Type $type) {
            return $type instanceof ArrayType
                && $type->innerType() instanceof MixedType;
        })) > 0;
        $createIterable = (! $createCollection && ! $createArray) || count(array_filter($types, function (Type $type) {
            return $type instanceof IterableType
                && $type->innerType() instanceof MixedType;
        })) > 0;

        $traversables = [];
        $innerTypes = [];
        foreach ($types as $each) {
            if ($each instanceof ArrayType && ! $each->innerType() instanceof MixedType) {
                $innerTypes[] = $each->innerType();
                continue;
            }
            if ($each instanceof IterableType && ! $each->innerType() instanceof MixedType) {
                $innerTypes[] = $each->innerType();
            }
            if ($each instanceof ClassType && $each->implementsOrIsSubClassOf(Traversable::class)) {
                $traversables[] = $each;
            }
        }
        $resultTypes = [];
        if (count($innerTypes) === 0) {
            return $type;
        }

        $innerType = count($innerTypes) > 1
            ? new ComposedType(...$innerTypes)
            : current($innerTypes);

        if ($createArray) {
            $resultTypes[] = new ArrayType($innerType);
        }
        if ($createIterable) {
            $resultTypes[] = new IterableType($innerType);
        }

        foreach ($traversables as $each) {
            $resultTypes[] = new CollectionType($each, $innerType);
        }

        return count($resultTypes) === 1 ? current($resultTypes) : new ComposedType(...$resultTypes);
    }

    /**
     * @param Type[] $types
     * @param Type[] $otherTypes
     * @return Type[]
     */
    private function diff(array $types, array $otherTypes): array
    {
        return array_diff($types, $otherTypes);
    }
}
