<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\EmptyClasses;
use IteratorIterator;
use const SORT_REGULAR;
use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;

class Classes extends IteratorIterator implements Countable
{
    /** @var ClassReflection[] */
    private $classes;

    public function __construct(ClassReflection ...$classes)
    {
        if (count($classes) > 0) {
            $classes = array_unique($classes, SORT_REGULAR);
        }
        parent::__construct(new ArrayIterator($classes));
        $this->classes = $classes;
    }

    public function count()
    {
        return count($this->classes);
    }

    /** @return ClassReflection|false */
    public function current()
    {
        return parent::current();
    }

    public function filter(callable $filter): Classes
    {
        return new self(...array_filter($this->classes, $filter));
    }

    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->classes);
    }

    /** @throws EmptyClasses */
    public function first(): ClassReflection
    {
        if ($this->count() === 0) {
            throw EmptyClasses::butFirstOneHasBeenRetrieved();
        }
        return current($this->classes);
    }

    public function merge(Classes ...$classes): Classes
    {
        return new self(...array_merge(
            $this->classes,
            ...array_map(
                function (Classes $classes) {
                    return $classes->classes;
                },
                $classes
            )
        ));
    }
}
