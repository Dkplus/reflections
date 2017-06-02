<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\MissingMethod;
use IteratorIterator;
use function array_combine;
use function array_map;

class Methods extends IteratorIterator implements Countable
{
    /** @var string */
    private $className;

    /** @var array */
    private $methods;

    public function __construct(string $className, MethodReflection ...$methods)
    {
        parent::__construct(new ArrayIterator($methods));
        $this->className = $className;
        $this->methods = array_combine(
            array_map(function (MethodReflection $method) {
                return $method->name();
            }, $methods),
            $methods
        );
    }

    /** @return MethodReflection|false */
    public function current()
    {
        return parent::current();
    }

    public function count(): int
    {
        return count($this->methods);
    }

    public function contains(string $name): bool
    {
        return isset($this->methods[$name]);
    }

    public function named(string $name): MethodReflection
    {
        if ($this->contains($name)) {
            return $this->methods[$name];
        }
        throw MissingMethod::inClass($name, $this->className);
    }
}
