<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use ReflectionClass;

interface ClassReflector
{
    public function reflect(string $className): ReflectionClass;
}
