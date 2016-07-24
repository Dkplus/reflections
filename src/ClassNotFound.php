<?php
namespace Dkplus\Reflections;

use Exception;

/**
 * @api
 */
class ClassNotFound extends Exception
{
    public static function named(string $className): self
    {
       return new self("Class $className not found");
    }
}
