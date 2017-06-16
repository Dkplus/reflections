<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\ClassReflector;

use Dkplus\Reflection\DocBlock\ClassReflector;
use ReflectionClass;

final class BuiltInClassReflector implements ClassReflector
{
    public function reflect(string $className): ReflectionClass
    {
        return new ReflectionClass($className);
    }
}
