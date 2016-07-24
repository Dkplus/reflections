<?php
namespace Dkplus\Reflections;

/**
 * @api
 */
interface Reflector
{
    public function reflectClass(string $className): ClassReflection;
}
