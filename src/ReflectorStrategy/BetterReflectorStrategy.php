<?php
declare(strict_types=1);

namespace Dkplus\Reflection\ReflectorStrategy;

use BetterReflection\Reflector\ClassReflector;
use BetterReflection\SourceLocator\Type\SourceLocator;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\MethodReflection;
use Dkplus\Reflection\PropertyReflection;
use Dkplus\Reflection\ReflectorStrategy;

final class BetterReflectorStrategy implements ReflectorStrategy
{
    /** @var ClassReflector */
    private $classReflector;

    public function __construct(ClassReflector $classReflector, SourceLocator $locator)
    {
        $this->classReflector = $classReflector;
    }

    public function reflectClassesFromFile(string $file): array
    {
        $this->classReflector->getAllClasses()
    }

    public function reflectClass(string $className): ClassReflection
    {
        // TODO: Implement reflectClassLike() method.
    }

    public function reflectMethod(string $className, string $method): MethodReflection
    {
        // TODO: Implement reflectMethod() method.
    }

    public function reflectProperty(string $className, string $property): PropertyReflection
    {
        // TODO: Implement reflectProperty() method.
    }
}
