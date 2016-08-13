<?php

namespace Dkplus\Reflections;

class Properties
{
    /** @var string */
    private $className;

    /** @var Property[] */
    private $properties;

    public function __construct(string $className, array $properties)
    {
        $this->className = $className;
        $this->properties = array_combine(
            array_map(function (Property $reflection) {
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

    public function named(string $name): Property
    {
        if ($this->contains($name)) {
            return $this->properties[$name];
        }
        throw MissingProperty::inClass($name, $this->className);
    }
}
