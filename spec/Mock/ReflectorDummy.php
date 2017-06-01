<?php
namespace spec\Dkplus\Reflection\Mock;

use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Reflector;
use RuntimeException;

final class ReflectorDummy implements Reflector
{
    public function reflectClassLike(string $className): ClassReflection
    {
        throw new RuntimeException(__CLASS__ . ' is a dummy that should not be called');
    }
}
