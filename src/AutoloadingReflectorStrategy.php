<?php
namespace Dkplus\Reflection;

use BetterReflection\Reflector\ClassReflector;
use BetterReflection\Reflector\Exception\IdentifierNotFound;
use BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use BetterReflection\SourceLocator\Type\EvaledCodeSourceLocator;
use BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Composer\Autoload\ClassLoader;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\Scanner\AnnotationScanner;
use Dkplus\Reflection\Scanner\ImportScanner;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use Dkplus\Reflections\BetterReflectionClassReflection;

final class AutoloadingReflectorStrategy implements ReflectorStrategy
{
    /** @var ImportScanner */
    private $importScanner;

    /** @var AnnotationScanner */
    private $annotationsScanner;

    /** @var ClassReflector */
    private $classReflector;

    /** @var ClassLoader */
    private $classLoader;

    /** @var TypeFactory */
    private $typeFactory;

    public function __construct(TypeFactory $typeFactory)
    {
        $this->importScanner = new ImportScanner();
        $this->annotationsScanner = new AnnotationScanner();
        $this->classLoader = new ClassLoader();
        $this->classReflector = new ClassReflector(new AggregateSourceLocator([
            new PhpInternalSourceLocator(),
            new EvaledCodeSourceLocator(),
            new ComposerSourceLocator($this->classLoader),
            new AutoloadSourceLocator()
        ]));
        $this->typeFactory = $typeFactory;
    }

    public function reflectClass(string $className): ClassReflection
    {
        try {
            $reflection = $this->classReflector->reflect($className);
            return new BetterReflectionClassReflection(
                $reflection,
                $this->annotationsScanner,
                $this,
                $this->typeFactory,
                $reflection->getFileName() ? $this->importScanner->scanForImports($reflection->getFileName()) : []
            );
        } catch (IdentifierNotFound $exception) {
            throw ClassNotFound::named($className);
        }
    }

    public function addPsr4Path(string $namespace, string $directory)
    {
        $this->classLoader->addPsr4($namespace, $directory);
    }

    public function addClassInFile(string $className, string $filePath)
    {
        $this->classLoader->addClassMap([$className => $filePath]);
    }
}
