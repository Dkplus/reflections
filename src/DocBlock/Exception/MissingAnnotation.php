<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\Exception;

use RuntimeException;

/** @package Dkplus\Reflection\DocBlock */
final class MissingAnnotation extends RuntimeException
{
    public static function withTag(string $tag): self
    {
        return new self("There is no annotation of tag @$tag within the doc block");
    }
}
