<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\MissingParameter;
use Dkplus\Reflection\Type\Type;
use IteratorIterator;
use function array_combine;
use function array_map;
use function array_shift;
use function array_values;

class Parameters extends IteratorIterator implements Countable
{
    /** @var string */
    private $method;

    /** @var ParameterReflection[] */
    private $parameters;

    public function __construct(string $method, ParameterReflection ...$parameters)
    {
        parent::__construct(new ArrayIterator($parameters));
        $this->method = $method;
        $this->parameters = array_combine(
            array_map(function (ParameterReflection $reflection) {
                return $reflection->name();
            }, $parameters),
            $parameters
        );
    }

    /** @return ParameterReflection|false */
    public function current()
    {
        return parent::current();
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    public function contains(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function named(string $name): ParameterReflection
    {
        if ($this->contains($name)) {
            return $this->parameters[$name];
        }
        throw MissingParameter::named($name, $this->method);
    }

    public function atPosition(int $position): ParameterReflection
    {
        if ($position >= 0 && $this->count() > $position) {
            return array_values($this->parameters)[$position];
        }
        throw MissingParameter::atPosition($position, $this->method);
    }

    public function allows(Type ...$types): bool
    {
        $parameters = array_values($this->parameters);
        /* @var $parameter ParameterReflection */
        while ($parameter = array_shift($parameters)) {
            $type = array_shift($types);
            if (! $type && ! $parameter->canBeOmitted()) {
                return false;
            }
            if ($type && ! $parameter->allows($type)) {
                return false;
            }
        }
        return true;
    }
}
