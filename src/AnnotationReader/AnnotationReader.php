<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use Dkplus\Reflection\AnnotationReader\Reflection\ReflectionFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use Dkplus\Reflection\AnnotationReader\Parser\DocParser;
use Dkplus\Reflection\AnnotationReader\Parser\MetadataParser;
use Dkplus\Reflection\AnnotationReader\Parser\PhpParser;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;

final class AnnotationReader implements Reader
{
    /** @var PhpParser */
    private $phpParser;

    /** @var DocParser */
    private $docParser;

    /** @var MetadataParser */
    private $metadataParser;

    /** @var ReflectionFactory */
    private $reflectionFactory;

    /** @var IgnoredAnnotationNames */
    private $ignoredAnnotationNames;

    public function __construct(Configuration $config = null)
    {
        if ($config === null) {
            $config = new Configuration();
        }

        $this->phpParser              = $config->getPhpParser();
        $this->docParser              = $config->getDocParser();
        $this->metadataParser         = $config->getMetadataParser();
        $this->reflectionFactory      = $config->getReflectionFactory();
        $this->ignoredAnnotationNames = $config->getIgnoredAnnotationNames();
    }

    /**
     * {@inheritDoc}
     */
    public function getClassAnnotations(ReflectionClass $class) : array
    {
        if (($docblock = $class->getDocComment()) === false) {
            return [];
        }

        $className  = $class->getName();
        $namespace  = $class->getNamespaceName();
        $reflection = $this->reflectionFactory->getReflectionClass($className);

        $imports = $reflection->getImports();
        $ignored = $this->getIgnoredAnnotationNames($class);
        $context = new Context($class, [$namespace], $imports, $ignored);

        return $this->docParser->parse($docblock, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassAnnotation(ReflectionClass $class, string $annotationName)
    {
        $annotations = $this->getClassAnnotations($class);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $annotationName) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotations(ReflectionProperty $property) : array
    {
        if (($docblock = $property->getDocComment()) === false) {
            return [];
        }

        $propertyName = $property->getName();
        $class        = $property->getDeclaringClass();

        $className  = $class->getName();
        $namespace  = $class->getNamespaceName();
        $reflection = $this->reflectionFactory->getReflectionProperty($className, $propertyName);

        $imports = $reflection->getImports();
        $ignored = $this->getIgnoredAnnotationNames($class);
        $context = new Context($property, [$namespace], $imports, $ignored);

        return $this->docParser->parse($docblock, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotation(ReflectionProperty $property, string $annotationName)
    {
        $annotations = $this->getPropertyAnnotations($property);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $annotationName) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotations(ReflectionMethod $method) : array
    {
        if (($docblock = $method->getDocComment()) === false) {
            return [];
        }

        $methodName = $method->getName();
        $class      = $method->getDeclaringClass();

        $className  = $class->getName();
        $namespace  = $class->getNamespaceName();
        $reflection = $this->reflectionFactory->getReflectionMethod($className, $methodName);

        $imports = $reflection->getImports();
        $ignored = $this->getIgnoredAnnotationNames($class);
        $context = new Context($method, [$namespace], $imports, $ignored);

        return $this->docParser->parse($docblock, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotation(ReflectionMethod $method, string $annotationName)
    {
        $annotations = $this->getMethodAnnotations($method);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $annotationName) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * Returns the ignored annotations for the given class.
     *
     * @param \ReflectionClass $class
     *
     * @return array
     */
    private function getIgnoredAnnotationNames(ReflectionClass $class) : array
    {
        $ignoredNames = $this->ignoredAnnotationNames->getArrayCopy();
        $annotations  = $this->metadataParser->parseAnnotationClass($class);

        foreach ($annotations as $annotation) {
            if ( ! $annotation instanceof IgnoreAnnotation) {
                continue;
            }

            foreach ($annotation->names as $name) {
                $ignoredNames[$name] = true;
            }
        }

        return $ignoredNames;
    }
}