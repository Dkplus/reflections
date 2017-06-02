<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\MissingProperty;
use IteratorIterator;
use function array_combine;
use function array_map;

class Properties extends IteratorIterator implements Countable
{
    /** @var string */
    private $className;

    /** @var PropertyReflection[] */
    private $properties;

    public function __construct(string $className, PropertyReflection  ...$properties)
    {
        parent::__construct(new ArrayIterator($properties));
        $this->className = $className;
        $this->properties = array_combine(
            array_map(function (PropertyReflection $reflection) {
                return $reflection->name();
            }, $properties),
            $properties
        );
    }

    /** @return PropertyReflection|false */
    public function current()
    {
        return parent::current();
    }

    public function count(): int
    {
        return count($this->properties);
    }

    public function contains(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function named(string $name): PropertyReflection
    {
        if ($this->contains($name)) {
            return $this->properties[$name];
        }
        throw MissingProperty::inClass($name, $this->className);
    }
}
