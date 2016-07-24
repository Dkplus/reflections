<?php
namespace Dkplus\Reflections\Type;

use Dkplus\Reflections\ClassReflection;

class ClassType implements Type
{
    /** @var ClassReflection */
    private $reflection;

    public function __construct(ClassReflection $reflection)
    {
        $this->reflection = $reflection;
    }

    public function allows(Type $type): bool
    {
        if (! $type instanceof self) {
            return false;
        }
        if ($type->reflection->name() === $this->reflection->name()) {
            return true;
        }
        return $type->reflection->implementsInterface($this->reflection->name())
            || $type->reflection->isSubclassOf($this->reflection->name());
    }

    public function __toString(): String
    {
        return $this->reflection->name();
    }

    public function reflection(): ClassReflection
    {
        return $this->reflection;
    }
}
