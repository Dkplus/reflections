<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

interface ReflectorStrategy
{
    public function reflectClass(string $className): ClassReflection;
}
