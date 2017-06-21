<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use function array_map;
use function is_array;
use function json_encode;

final class AnnotationReflection
{
    /** @var string */
    private $tag;

    /** @var bool */
    private $fullyQualified = false;

    /** @var array */
    private $attributes;

    /** @var Annotations */
    private $immediatelyAttached;

    private static function toString(array $data): string
    {
        $strings = array_map(function ($attribute) {
            if (is_array($attribute)) {
                return self::toString($attribute);
            }
            return (string) $attribute;
        }, $data);
        return json_encode($strings);
    }

    public static function fullyQualified(
        string $tag,
        array $attributes,
        AnnotationReflection ...$immediatelyInherited
    ): self {
        $result = new self(ltrim($tag, '\\'), $attributes, new Annotations(...$immediatelyInherited));
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
        $this->immediatelyAttached = $immediatelyInherited;
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

    public function attached(): Annotations
    {
        if (count($this->immediatelyAttached) === 0) {
            return $this->immediatelyAttached;
        }
        return $this->immediatelyAttached->includeAttached();
    }

    public function __toString(): string
    {
        $inherited = $this->attached()->map(function (AnnotationReflection $reflection) {
            return (string) $reflection;
        });
        return '@' . $this->tag . '(' . self::toString($this->attributes) . '): ' . self::toString($inherited);
    }
}
