<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Adapter\BetterReflection;

use BetterReflection\Reflector\ClassReflector;
use BetterReflection\SourceLocator\Type\SourceLocator;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\MethodReflection;
use Dkplus\Reflection\PropertyReflection;
use Dkplus\Reflection\Reflector;

final class BetterReflector implements Reflector
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

    public function reflectClassLike(string $className): ClassReflection
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
