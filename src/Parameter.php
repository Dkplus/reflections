<?php
namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionParameter;

class Parameter
{
    /** @var ReflectionParameter */
    private $reflection;

    public function __construct(ReflectionParameter $parameter)
    {
        $this->reflection = $parameter;
    }

    public function name()
    {
        return $this->reflection->getName();
    }

    public function type()
    {
        return $this->reflection->getType()
            ? (string) $this->reflection->getType()->getTypeObject()
            : 'mixed';
    }

    public function allowsNull()
    {
        return $this->reflection->getType() ? $this->reflection->getType()->allowsNull() : true;
    }
}
