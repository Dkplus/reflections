<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Dkplus\Reflection\ClassReflection;
use ReflectionClass;

class ClassType implements Type
{
    /** @var ReflectionClass */
    private $reflection;

    public function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
    }

    public function allows(Type $type): bool
    {
        if (! $type instanceof self) {
            return false;
        }
        if ($type->reflection->name == $this->reflection->name) {
            return true;
        }
        return $type->reflection->implementsInterface($this->reflection->name)
            || $type->reflection->isSubclassOf($this->reflection->name);
    }

    public function __toString(): string
    {
        return (string) '\\' . $this->reflection->name;
    }

    public function isInvokable(): bool
    {
        return $this->reflection->hasMethod('__invoke');
    }
}
