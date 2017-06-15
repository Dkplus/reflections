<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

final class DocBlockReflection
{
    /** @var string */
    private $shortDescription;

    /** @var string */
    private $longDescription;

    /** @var Annotations */
    private $annotations;

    public function __construct(string $shortDescription, string $longDescription, Annotations $annotations)
    {
        $this->shortDescription = $shortDescription;
        $this->longDescription = $longDescription;
        $this->annotations = $annotations;
    }

    public function shortDescription(): string
    {
        return $this->shortDescription;
    }

    public function longDescription(): string
    {
        return $this->longDescription;
    }

    public function annotations(): Annotations
    {
        return $this->annotations;
    }
}
