<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class MissingAnnotation extends Exception
{
    public static function named(string $name): self
    {
        return new self("There is no annotation of name $name within the doc block");
    }
}
