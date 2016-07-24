<?php

namespace Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionType;

class TypeReflection
{
    /** @var ReflectionType */
    private $reflectionType;

    /** @var array */
    private $phpDocTypes;

    /** @var Reflector */
    private $reflections;

    public function __construct(
        Reflector $reflections,
        ReflectionType $reflectionType = null,
        array $phpDocTypes = []
    ) {
        $this->reflectionType = $reflectionType;
        $this->phpDocTypes = $phpDocTypes;
        $this->reflections = $reflections;
    }

    public function __toString(): string
    {
        if (! $this->reflectionType) {
            if (count($this->phpDocTypes) === 1) {
                return current($this->phpDocTypes);
            }
            return 'mixed';
        }
        $result = (string) $this->reflectionType->getTypeObject();
        if ($result === 'array' && substr(current($this->phpDocTypes), -2) === '[]') {
            return current($this->phpDocTypes);
        }
        return $result;
    }

    public function allows(string $type): bool
    {
        $nonObjectClasses = ['string', 'float', 'int', 'bool', 'resource', 'callable', 'array'];
        $currentType = $this->__toString();

        switch($currentType) {
            case 'mixed':
                return $type !== 'void';
            case 'null':
                return $this->reflectionType ? $this->reflectionType->allowsNull() : true;
            case 'void':
                return false;
        }

        // scalar and resource
        if (in_array($currentType, ['string', 'float', 'int', 'bool', 'resource'])) {
            return $type === $this->__toString();
        }

        // callable
        if ($currentType === 'callable') {
            try {
                return $type === 'callable'
                    || $type === 'array'
                    || (! in_array($type, $nonObjectClasses)
                        && substr($type, -2) !== '[]'
                        && $this->reflections->reflectClass($type)->isInvokable());
            } catch (ClassNotFound $exception) {
                return false;
            }
        }

        // mixed array
        if ($currentType === 'array') {
            return $type === 'array' || substr($type, -2) === '[]';
        }

        if (in_array($type, $nonObjectClasses)) {
            return false;
        }

        if (substr($type, -2) === '[]') {
            return false;
        }

        // only classes left
        if ($type === $currentType) {
            return true;
        }
        $reflection = $this->reflections->reflectClass($type);
        return $reflection->implementsInterface($currentType)
            || $reflection->isSubclassOf($currentType);
    }
}
