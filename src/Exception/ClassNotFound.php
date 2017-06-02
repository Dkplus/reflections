<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class ClassNotFound extends Exception
{
    public static function named(string $className): self
    {
        return new self("Class $className not found");
    }
}
