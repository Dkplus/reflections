<?php
namespace Dkplus\Reflections;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

/**
 * @api
 */
final class Annotations implements IteratorAggregate
{
    /** @var array */
    private $annotations;

    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    public function all(): array
    {
        return $this->annotations;
    }

    public function oneOfClass(string $className)
    {
        $ofClass = $this->ofClass($className);
        if (count($ofClass) === 0) {
            throw MissingAnnotation::ofClass($className);
        }
        return current($ofClass);
    }

    public function contains(string $className): bool
    {
        return count($this->ofClass($className)) > 0;
    }

    public function ofClass(string $className): array
    {
        return array_filter($this->annotations, function ($annotation) use ($className) {
            return $annotation instanceof $className;
        });
    }

    public function getIterator(): Iterator
    {
         return new ArrayIterator($this->annotations);
    }}
