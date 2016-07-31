<?php
namespace Dkplus\Reflections;

use Dkplus\Reflections\Type\Type;

class Parameters
{
    /** @var string */
    private $method;

    /** @var Parameter[] */
    private $parameters;

    public function __construct(string $method, array $parameters)
    {
        $this->method = $method;
        $this->parameters = array_combine(
            array_map(function (Parameter $reflection) {
                return $reflection->name();
            }, $parameters),
            $parameters
        );
    }

    public function size(): int
    {
        return count($this->parameters);
    }

    public function contains(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function all(): array
    {
        return array_values($this->parameters);
    }

    public function named(string $name): Parameter
    {
        if ($this->contains($name)) {
            return $this->parameters[$name];
        }
        throw MissingParameter::named($name, $this->method);
    }

    public function atPosition(int $position): Parameter
    {
        if ($position >= 0 && $this->size() > $position) {
            return array_values($this->parameters)[$position];
        }
        throw MissingParameter::atPosition($position, $this->method);
    }

    public function allows(Type ...$types)
    {
        foreach ($types as $position => $type) {
            if (! $this->atPosition($position)->allows($type)) {
                return false;
            }
        }
        if ($this->size() > count($types)) {
            for ($i = count($types); $i < $this->size(); ++$i) {
                if (! $this->atPosition($i)->canBeOmitted()) {
                    return false;
                }
            }
        }
        return true;
    }
}
