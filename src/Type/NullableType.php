<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

final class NullableType implements DecoratingType
{
    /** @var Type */
    private $decorated;

    public function __construct(Type $decorated)
    {
        $this->decorated = $decorated;
    }

    public function accepts(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'accepts'], $type->innerTypes()));
        }
        if ($type instanceof NullType) {
            return true;
        }
        if ($type instanceof self) {
            return $this->decorated->accepts($type->decorated);
        }
        return $this->decorated->accepts($type);
    }

    public function __toString(): string
    {
        if ($this->decorated instanceof ComposedType) {
            return "?({$this->decorated})";
        }
        return '?' . $this->decorated;
    }

    public function innerType(): Type
    {
        return $this->decorated;
    }
}
