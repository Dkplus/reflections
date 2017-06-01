<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

interface Reflector
{
    public function reflectClassLike(string $className): ClassReflection;
    public function reflectMethod(string $className, string $method): MethodReflection;
    public function reflectProperty(string $className, string $property): PropertyReflection;
}
