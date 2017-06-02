<?php
declare(strict_types=1);

namespace Dkplus\Reflection\ReflectorStrategy;

use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Methods;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\ReflectorStrategy;
use ReflectionClass;
use function array_filter;
use function array_map;

final class BuiltInReflectorStrategy implements ReflectorStrategy
{
    public function reflectClass(string $className): ClassReflection
    {
        $class = new ReflectionClass($className);

        $parents = $class->getParentClass() ? [$class->getParentClass()->name] : [];
        $interfaces = $class->getInterfaceNames();
        $traits = $class->getTraitNames();
        if ($class->isInterface()) {
            $parents = $this->filterNonImmediateInterfaces($class->getInterfaceNames(), $class->getInterfaceNames());
            $interfaces = [];
        }

        $parents = new Classes(...array_map([$this, 'reflectClass'], $parents));

        $possibleParents = $interfaces;
        if ($parents->count() > 0) {
            $possibleParents[] = $parents->first()->name();
        }
        $interfaces = $this->filterNonImmediateInterfaces($interfaces, $possibleParents);
        $interfaces = new Classes(...array_map([$this, 'reflectClass'], $interfaces));

        $traits = new Classes(...array_map([$this, 'reflectClass'], $traits));

        return new ClassReflection($class, $parents, $interfaces, $traits, new Properties($className), new Methods($className));
    }

    private function filterNonImmediateInterfaces(array $interfaces, array $possibleParents): array
    {
        /* @var $reflectionsOfParents ReflectionClass[] */
        $reflectionsOfParents = array_map(function (string $className) {
            return new ReflectionClass($className);
        }, $possibleParents);

        return array_filter($interfaces, function (string $interface) use ($reflectionsOfParents) {
            foreach ($reflectionsOfParents as $each) {
                if ($each->name !== $interface && $each->implementsInterface($interface)) {
                    return false;
                }
            }
            return true;
        });
    }
}
