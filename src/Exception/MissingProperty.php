<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class MissingProperty extends Exception
{
    public static function inClass(string $property, string $className): self
    {
        return new self("There is no property $property in class $className");
    }
}
