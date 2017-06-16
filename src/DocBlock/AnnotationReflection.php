<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

final class AnnotationReflection
{
    /** @var string */
    private $tag;

    /** @var bool */
    private $fullyQualified = false;

    /** @var array */
    private $attributes;

    /** @var string[] */
    private $providedTags;

    /** @var Annotations */
    private $immediatelyInherited;

    public static function fullyQualified(
        string $tag,
        array $attributes,
        AnnotationReflection ...$immediatelyInherited
    ): self {
        $result = new self($tag, $attributes, new Annotations(...$immediatelyInherited));
        $result->fullyQualified = true;
        return $result;
    }

    public static function unqualified(string $tag, array $attributes): self
    {
        return new self($tag, $attributes, new Annotations());
    }

    private function __construct(string $tag, array $attributes, Annotations $immediatelyInherited)
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->immediatelyInherited = $immediatelyInherited;
        $this->providedTags = array_unique(array_merge([$tag], $this->inherited()->map(function (self $annotation) {
            return $annotation->tag();
        })));
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function isFullyQualified(): bool
    {
        return $this->fullyQualified;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function inherited(): Annotations
    {
        if (count($this->immediatelyInherited) === 0) {
            return $this->immediatelyInherited;
        }
        return $this->immediatelyInherited->includeInherited();
    }
}
