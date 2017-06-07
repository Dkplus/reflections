<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use ReflectionClass;

class ClassType implements Type
{
    /** @var ReflectionClass */
    private $reflection;

    public function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
    }

    protected function reflection(): ReflectionClass
    {
        return $this->reflection;
    }

    public function className(): string
    {
        return '\\' . $this->reflection->getName();
    }

    public function accepts(Type $type): bool
    {
        if (! $type instanceof self) {
            return false;
        }
        if ($type->className() === $this->className()) {
            return true;
        }
        return $type->implementsOrIsSubClassOf($this->className());
    }

    public function implementsOrIsSubClassOf(string $className): bool
    {
        return $this->reflection->implementsInterface($className)
            || $this->reflection->isSubclassOf($className);
    }

    public function isInvokable(): bool
    {
        return $this->reflection->hasMethod('__invoke');
    }

    public function __toString(): string
    {
        return $this->className();
    }
}
