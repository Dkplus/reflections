<?php
namespace spec\Dkplus\Reflection\Mock;

use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\ReflectorStrategy;
use RuntimeException;

final class ReflectorStrategyDummy implements ReflectorStrategy
{
    public function reflectClass(string $className): ClassReflection
    {
        throw new RuntimeException(__CLASS__ . ' is a dummy that should not be called');
    }
}
