<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;

final class DocBlockReflection
{
    /** @var string */
    private $summary;

    /** @var string */
    private $description;

    /** @var Annotations */
    private $annotations;

    public function __construct(string $summary, string $description, AnnotationReflection ...$annotations)
    {
        $this->summary = $summary;
        $this->description = $description;
        $this->annotations = new Annotations(...$annotations);
    }

    public function summary(): string
    {
        return $this->summary;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function annotations(bool $withInherited = false): Annotations
    {
        if ($withInherited) {
            return $this->annotations->includeInherited();
        }
        return $this->annotations;
    }

    public function hasTag(string $tag, bool $includeInherited = false): bool
    {
        return $this->annotations($includeInherited)->containsAtLeastOneWithTag($tag);
    }

    public function annotationsWithTag(string $name, bool $includeInherited = false): Annotations
    {
        return $this->annotations($includeInherited)->withTag($name);
    }

    public function countAnnotations(bool $includeInherited = false): int
    {
        return count($this->annotations($includeInherited));
    }

    /** @throws MissingAnnotation */
    public function oneAnnotationWithTag(string $named, bool $includeInherited = false): AnnotationReflection
    {
        return $this->annotations($includeInherited)->oneWithTag($named);
    }
}
