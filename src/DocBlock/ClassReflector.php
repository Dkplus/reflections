<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use ReflectionClass;
use ReflectionException;

interface ClassReflector
{
    /** @throws ReflectionException */
    public function reflect(string $className): ReflectionClass;
}
