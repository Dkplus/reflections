<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class MissingAnnotation extends Exception
{
    public static function ofClass(string $className): self
    {
        return new self("There is no annotation of class $className within the doc block");
    }
}
