<?php
declare(strict_types=1);

namespace Dkplus\Reflection\ReflectorStrategy;

use Dkplus\Reflection\Type\ClassReflector;
use ReflectionClass;

class BuiltInClassReflector implements ClassReflector
{
    public function reflect(string $className): ReflectionClass
    {
        return new ReflectionClass($className);
    }
}
