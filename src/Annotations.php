<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\Exception\MissingAnnotation;
use IteratorIterator;

class Annotations extends IteratorIterator implements Countable
{
    /** @var AnnotationReflection[] */
    private $annotations;

    public function __construct(AnnotationReflection ...$annotations)
    {
        parent::__construct(new ArrayIterator($annotations));
        $this->annotations = $annotations;
    }

    /** @return AnnotationReflection|null */
    public function current()
    {
        return parent::current();
    }

    public function count(): int
    {
        return count($this->annotations);
    }

    public function oneNamed(string $name): AnnotationReflection
    {
        $ofClass = $this->named($name);
        if (count($ofClass) === 0) {
            throw MissingAnnotation::named($name);
        }
        return current($ofClass->annotations);
    }

    public function contains(string $name): bool
    {
        return count($this->named($name)) > 0;
    }

    public function named(string $name): Annotations
    {
        return new self(...array_filter($this->annotations, function (AnnotationReflection $annotation) use ($name) {
            return $annotation->name() === $name;
        }));
    }
}
