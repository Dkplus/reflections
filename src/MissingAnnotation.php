<?php
namespace Dkplus\Reflections;

use Exception;

/**
 * @api
 */
class MissingAnnotation extends Exception
{
    public static function ofClass(string $className): self
    {
        return new self("There is no annotation of class $className within the doc block");
    }
}
