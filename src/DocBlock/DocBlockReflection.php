<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;
use function array_filter;
use function array_keys;
use function in_array;
use function str_replace;

final class DocBlockReflection
{
    /** @var string */
    private $summary;

    /** @var string */
    private $description;

    /** @var Annotations */
    private $annotations;

    /** @internal */
    public static function inherit(self $docBlock, self $parent): self
    {
        $inheritAnnotations = ['author' => true, 'copyright' => true, 'package' => true, 'version' => true];
        $annotations = [];
        foreach ($docBlock->annotations as $each) {
            $annotations[] = $each;
            if (isset($inheritAnnotations[$each->tag()])) {
                $inheritAnnotations[$each->tag()] = null;
            }
        }
        $inheritAnnotations = array_filter($inheritAnnotations);
        $inheritAnnotations = array_keys($inheritAnnotations);
        foreach ($parent->annotations() as $each) {
            if (in_array($each->tag(), $inheritAnnotations)) {
                $annotations[] = $each;
                continue;
            }
            if ($each->tag() === 'subpackage' && in_array('package', $inheritAnnotations)) {
                $annotations[] = $each;
            }
        }
        $description = $docBlock->description();
        if (strlen($description) == 0) {
            $description = $parent->description();
        } else {
            $description = str_replace(
                [' {@inheritdoc} ', ' {@inheritdoc}', '{@inheritdoc} ', '{@inheritdoc}'],
                [
                    $parent->description() ? ' ' . trim($parent->description()) . ' ' : ' ',
                    $parent->description() ? trim(' ' . $parent->description()) : ' ',
                    $parent->description() ? trim($parent->description() . ' ') : ' ',
                    $parent->description() ? trim($parent->description()) : '',
                ],
                $description
            );
        }
        return new self(
            strlen($docBlock->summary()) == 0 ? $parent->summary() : $docBlock->summary(),
            $description,
            ...$annotations
        );
    }

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

    public function annotations(bool $withAttached = false): Annotations
    {
        if ($withAttached) {
            return $this->annotations->includeAttached();
        }
        return $this->annotations;
    }

    public function hasTag(string $tag, bool $includeAttached = false): bool
    {
        return $this->annotations($includeAttached)->containsAtLeastOneWithTag($tag);
    }

    public function annotationsWithTag(string $name, bool $includeAttached = false): Annotations
    {
        return $this->annotations($includeAttached)->withTag($name);
    }

    public function countAnnotations(bool $includeAttached = false): int
    {
        return count($this->annotations($includeAttached));
    }

    /** @throws MissingAnnotation */
    public function oneAnnotationWithTag(string $tag, bool $includeAttached = false): AnnotationReflection
    {
        return $this->annotations($includeAttached)->oneWithTag($tag);
    }

    /**
     * @param mixed|null $default
     * @return mixed
     */
    public function oneAnnotationAttribute(string $tag, string $attribute, $default = null)
    {
        if (!$this->annotations()->containsAtLeastOneWithTag($tag)) {
            return $default;
        }
        return $this->oneAnnotationWithTag($tag)->attributes()[$attribute] ?? $default;
    }
}
