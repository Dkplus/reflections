<?php
namespace Dkplus\Reflection;

use Exception;

/**
 * @api
 */
class MissingProperty extends Exception
{
    public static function inClass(string $property, string $className): self
    {
        return new self("There is no property $property in class $className");
    }
}
