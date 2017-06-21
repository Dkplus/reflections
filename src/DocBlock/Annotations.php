<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use ArrayIterator;
use Countable;
use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;
use IteratorIterator;
use const SORT_REGULAR;
use function array_map;
use function array_merge;
use function array_unique;

class Annotations extends IteratorIterator implements Countable
{
    /** @var AnnotationReflection[] */
    private $annotations;

    public function __construct(AnnotationReflection ...$annotations)
    {
        parent::__construct(new ArrayIterator($annotations));
        $this->annotations = $annotations;
    }

    /** @return AnnotationReflection|false */
    public function current()
    {
        return parent::current();
    }

    public function count(): int
    {
        return count($this->annotations);
    }

    /** @throws MissingAnnotation */
    public function oneWithTag(string $tag): AnnotationReflection
    {
        $withTag = $this->withTag($tag);
        if (count($withTag) === 0) {
            throw MissingAnnotation::withTag($tag);
        }
        return current($withTag->annotations);
    }

    public function containsAtLeastOneWithTag(string $tag): bool
    {
        return count($this->withTag($tag)) > 0;
    }

    public function withTag(string $tag): Annotations
    {
        return new self(...array_filter($this->annotations, function (AnnotationReflection $annotation) use ($tag) {
            return $annotation->tag() === $tag;
        }));
    }

    public function map(callable $callable): array
    {
        return array_map($callable, $this->annotations);
    }

    public function merge(self ...$annotations): self
    {
        $result = array_merge(
            $this->annotations,
            ...array_map(function (self $annotations) {
                return $annotations->annotations;
            }, $annotations)
        );
        return new self(...array_unique($result, SORT_REGULAR));
    }

    public function includeAttached(): self
    {
        return $this->merge(...$this->map(function (AnnotationReflection $reflection) {
            return $reflection->attached();
        }));
    }
}
