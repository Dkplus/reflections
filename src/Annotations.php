<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\MissingAnnotation;
use IteratorIterator;

class Annotations extends IteratorIterator implements Countable
{
    /** @var array */
    private $annotations;

    public function __construct(array $annotations)
    {
        parent::__construct(new ArrayIterator($annotations));
        $this->annotations = $annotations;
    }

    public function count(): int
    {
        return count($this->annotations);
    }

    public function oneOfClass(string $className)
    {
        $ofClass = $this->ofClass($className);
        if (count($ofClass) === 0) {
            throw MissingAnnotation::ofClass($className);
        }
        return current($ofClass->annotations);
    }

    public function contains(string $className): bool
    {
        return count($this->ofClass($className)) > 0;
    }

    public function ofClass(string $className): Annotations
    {
        return new self(array_filter($this->annotations, function ($annotation) use ($className) {
            return $annotation instanceof $className;
        }));
    }
}
