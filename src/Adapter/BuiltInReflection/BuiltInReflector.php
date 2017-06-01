<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Adapter\BuiltInReflection;

use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\MethodReflection;
use Dkplus\Reflection\PropertyReflection;
use Dkplus\Reflection\Reflector;
use ReflectionClass;

final class BuiltInReflector implements Reflector
{
    public function reflectClassLike(string $className): ClassReflection
    {
        $class = new ReflectionClass($className);
    }

    public function reflectMethod(string $className, string $method): MethodReflection
    {
        return $this->reflectClassLike($className)->method($method);
    }

    public function reflectProperty(string $className, string $property): PropertyReflection
    {
        $class = $this->reflectClassLike($className);
        if (! $class instanceof StatefulClassAlike) {
            throw new RuntimeException();
        }
        return $class->property($property);
    }
}
