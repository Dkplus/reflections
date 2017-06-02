<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use ReflectionClass;

class ClassReflection
{
    /** @var ReflectionClass */
    private $reflection;

    /** @var ReflectionClass[] */
    private $immediateParentClasses;

    /** @var ReflectionClass[] */
    private $immediateImplementedInterfaces;

    /** @var ClassReflection[] */
    private $immediateUsedTraits;

    /** @var Properties */
    private $properties;

    /** @var Methods */
    private $methods;

    /**
     * @param ClassReflection[] $parentClasses
     * @param ClassReflection[] $parentClasses
     * @param ClassReflection[] $implementedInterfaces
     * @param ClassReflection[] $usedTraits
     */
    public function __construct(
        ReflectionClass $reflection,
        Classes $parentClasses,
        Classes $implementedInterfaces,
        Classes $usedTraits,
        Properties $properties,
        Methods $methods
    ) {
        $this->reflection = $reflection;
        $this->immediateParentClasses = $parentClasses;
        $this->immediateImplementedInterfaces = $implementedInterfaces;
        $this->immediateUsedTraits = $usedTraits;
        $this->properties = $properties;
        $this->methods = $methods;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function shortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function namespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function packageName(): string
    {
        $package = $this->reflection->getNamespaceName();
        $docComment = (string) $this->reflection->getDocComment();
        if (preg_match('/^\s*\* @package (.*)/m', $docComment, $matches)) {
            $package = $matches[1];
        }
        if (preg_match('/^\s*\* @subpackage (.*)/m', $docComment, $matches)) {
            $package = $package . '\\' . $matches[1];
        }
        return $package;
    }

    public function isInternal(): bool
    {
        return $this->reflection->isInternal();
    }

    public function isIterateable(): bool
    {
        return $this->reflection->isIterateable();
    }

    public function isInterface(): bool
    {
        return $this->reflection->isInterface();
    }

    public function isTrait(): bool
    {
        return $this->reflection->isTrait();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isInterface() || $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function immediateParentClasses(): Classes
    {
        return $this->immediateParentClasses;
    }

    public function parentClasses(): Classes
    {
        $getParentClasses = function (ClassReflection $parent) {
            return $parent->parentClasses();
        };
        return $this->immediateParentClasses->merge(...$this->immediateParentClasses->map($getParentClasses));
    }

    public function extendsClass(string $className): bool
    {
        return $this->reflection->isSubclassOf($className);
    }

    public function implementsInterface(string $interface): bool
    {
        return $this->reflection->implementsInterface($interface);
    }

    public function immediateImplementedInterfaces(): Classes
    {
        return $this->immediateImplementedInterfaces;
    }

    public function implementedInterfaces(): Classes
    {
        $getInterfaces = function (ClassReflection $class) {
            return $class->implementedInterfaces();
        };
        $getParents = function (ClassReflection $class) {
            return $class->parentClasses();
        };
        return $this->immediateImplementedInterfaces->merge(
            ...$this->immediateParentClasses->map($getInterfaces),
            ...$this->immediateImplementedInterfaces->map($getParents)
        );
    }

    public function immediateUsedTraits(): Classes
    {
        return $this->immediateUsedTraits;
    }

    public function usedTraits(): Classes
    {
        $getTraits = function (ClassReflection $class) {
            return $class->usedTraits();
        };
        return $this->immediateUsedTraits->merge(
            ...$this->immediateParentClasses->map($getTraits),
            ...$this->immediateUsedTraits->map($getTraits)
        );
    }

    public function property(string $name): PropertyReflection
    {
    }

    public function method(string $name): MethodReflection
    {
    }
}
