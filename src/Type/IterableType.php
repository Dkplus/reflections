<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Traversable;

final class IterableType implements DecoratingType
{
    /** @var Type */
    private $type;

    public function __construct(Type $type = null)
    {
        $this->type = $type ?: new MixedType();
    }

    public function innerType(): Type
    {
        return $this->type;
    }

    public function accepts(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'accepts'], $type->innerTypes()));
        }
        if ($this->type instanceof MixedType && $type instanceof ClassType) {
            return $type->implementsOrIsSubClassOf(Traversable::class);
        }
        if ($type instanceof self || $type instanceof ArrayType || $type instanceof CollectionType) {
            return $this->innerType()->accepts($type->decoratedType());
        }
        return false;
    }

    public function __toString(): string
    {
        if ($this->type instanceof ComposedType) {
            return "({$this->type})[]";
        }
        return $this->type . '[]';
    }
}
