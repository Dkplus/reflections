<?php
namespace Dkplus\Reflections;

use Exception;

/**
 * @api
 */
final class MissingProperty extends Exception
{
    public static function inClass(string $property, string $className): self
    {
        return new self("There is no property $property in class $className");
    }
}
