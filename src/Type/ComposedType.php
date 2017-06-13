<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

class ComposedType implements Type
{
    /** @var Type[] */
    private $types;

    public function __construct(Type $firstType, Type $secondType, Type ...$moreTypes)
    {
        $this->types = array_merge([$firstType, $secondType], $moreTypes);
    }

    public function accepts(Type $type): bool
    {
        return in_array(true, array_map(function (Type $inner) use ($type) {
            return $inner->accepts($type);
        }, $this->innerTypes()));
    }

    public function __toString(): string
    {
        return implode('|', array_map('strval', $this->innerTypes()));
    }

    /** @return Type[] */
    public function innerTypes(): array
    {
        return $this->types;
    }
}
