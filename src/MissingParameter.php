<?php
namespace Dkplus\Reflection;

use Exception;

/**
 * @api
 */
class MissingParameter extends Exception
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
