<?php
namespace spec\Dkplus\Reflections\Mock;

use Dkplus\Reflections\ClassReflection;
use Dkplus\Reflections\Reflector;
use RuntimeException;

final class ReflectorDummy implements Reflector
{
    public function reflectClass(string $className): ClassReflection
    {
        throw new RuntimeException(__CLASS__ . ' is a dummy that should not be called');
    }
}
