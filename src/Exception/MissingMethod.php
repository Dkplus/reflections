<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class MissingMethod extends Exception
{
    public static function inClass(string $method, string $className): self
    {
        return new self("There is no method $method in class $className");
    }
}
