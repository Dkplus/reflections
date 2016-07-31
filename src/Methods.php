<?php

namespace Dkplus\Reflections;

class Methods
{
    /** @var string */
    private $className;

    /** @var array */
    private $methods;

    public function __construct(string $className, array $methods)
    {
        $this->className = $className;
        $this->methods = array_combine(
            array_map(function (Method $method) {
                return $method->name();
            }, $methods),
            $methods
        );
    }

    public function size(): int
    {
        return count($this->methods);
    }

    public function contains(string $name): bool
    {
        return isset($this->methods[$name]);
    }

    public function all(): array
    {
        return array_values($this->methods);
    }

    public function named(string $name): Method
    {
        if ($this->contains($name)) {
            return $this->methods[$name];
        }
        throw MissingMethod::inClass($name, $this->className);
    }

    public function containsGetterFor(string $property)
    {
        return count(array_filter($this->methods, function (Method $reflection) use ($property) {
            return $reflection->isGetterOf($property);
        })) > 0;
    }
}
