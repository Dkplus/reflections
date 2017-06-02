<?php
namespace spec\Dkplus\Reflection\Mock;

use Dkplus\Reflection\ClassReflection_;
use Dkplus\Reflection\ReflectorStrategy;
use RuntimeException;

final class ReflectorStrategyDummy implements ReflectorStrategy
{
    public function reflectClass(string $className): ClassReflection_
    {
        throw new RuntimeException(__CLASS__ . ' is a dummy that should not be called');
    }
}
