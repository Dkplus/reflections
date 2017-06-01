<?php

namespace Dkplus\Reflection;

class Properties
{
    /** @var string */
    private $className;

    /** @var PropertyReflection[] */
    private $properties;

    public function __construct(string $className, array $properties)
    {
        $this->className = $className;
        $this->properties = array_combine(
            array_map(function (PropertyReflection $reflection) {
                return $reflection->name();
            }, $properties),
            $properties
        );
    }

    public function size(): int
    {
        return count($this->properties);
    }

    public function contains(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function all(): array
    {
        return array_values($this->properties);
    }

    public function named(string $name): PropertyReflection
    {
        if ($this->contains($name)) {
            return $this->properties[$name];
        }
        throw MissingProperty::inClass($name, $this->className);
    }
}
