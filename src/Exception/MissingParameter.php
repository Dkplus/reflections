<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Exception;

final class MissingParameter extends Exception
{
    public static function named(string $parameter, string $method): self
    {
        return new self("There is no parameter named $parameter in method $method");
    }

    public static function atPosition(int $position, string $method): self
    {
        return new self("There is no parameter at position $position in method $method");
    }
}
