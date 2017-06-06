<?php
declare(strict_types=1);

namespace Dkplus\Reflection\ReflectorStrategy;

use Dkplus\Reflection\Annotation\AnnotationFactory;
use Dkplus\Reflection\Annotation\AnnotationReflector;
use Dkplus\Reflection\Annotation\FqcnResolver;
use Dkplus\Reflection\Annotation\HoaParser;
use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\Methods;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Scanner\ImportScanner;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
use ReflectionException;
use function array_filter;
use function array_map;

final class BuiltInReflectorStrategy implements ReflectorStrategy
{
    /** @var AnnotationReflector */
    private $annotationReflector;

    /** @var ContextFactory */
    private $contextFactory;

    public function __construct()
    {
        $this->annotationReflector = new AnnotationReflector(
            new HoaParser(),
            new AnnotationFactory($this, new FqsenResolver())
        );
        $this->contextFactory = new ContextFactory();
    }

    public function reflectClass(string $className): ClassReflection
    {
        try {
            $class = new ReflectionClass($className);
        } catch (ReflectionException $error) {
            throw new ClassNotFound("Class $className could not be reflected");
        }

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

        $context = $this->contextFactory->createFromReflector($class);
        $annotations = $this->annotationReflector->reflectDocBlock($class->getDocComment(), $context);

        return new ClassReflection(
            $class,
            $annotations,
            $parents,
            $interfaces,
            $traits,
            new Properties($className),
            new Methods($className)
        );
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
