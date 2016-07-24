<?php
namespace Dkplus\Reflections;

use Exception;

/**
 * @api
 */
class MissingMethod extends Exception
{
    public static function inClass(string $method, string $className): self
    {
        return new self("There is no method $method in class $className");
    }
}
