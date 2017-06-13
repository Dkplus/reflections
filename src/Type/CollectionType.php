<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Traversable;

class CollectionType extends ClassType implements DecoratingType
{
    /** @var ClassType */
    private $class;

    /** @var IterableType */
    private $iterableGeneric;

    public function __construct(ClassType $class, Type $generic)
    {
        $this->class = $class;
        $this->iterableGeneric = new IterableType($generic);
        if (! $class->implementsOrIsSubClassOf(Traversable::class)) {
            throw new InvalidArgumentException('Class ' . $class->className() . ' is not traversable');
        }
        parent::__construct($class->reflection());
    }

    public function accepts(Type $type): bool
    {
        return $type instanceof self
            && $this->class->accepts($type->class)
            && $this->iterableGeneric->accepts($type->iterableGeneric);
    }

    public function innerType(): Type
    {
        return $this->iterableGeneric->innerType();
    }

    public function __toString(): string
    {
        return "{$this->class}|{$this->iterableGeneric}";
    }
}
