<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Interface for annotation readers.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Reader
{
    /**
     * Gets the annotations applied to a class.
     *
     * @param \ReflectionClass $class The ReflectionClass of the class from which
     *                                the class annotations should be read.
     *
     * @return array An array of Annotations.
     */
    public function getClassAnnotations(ReflectionClass $class): array;

    /**
     * Gets a class annotation.
     *
     * @param \ReflectionClass $class The ReflectionClass of the class from which
     *                                         the class annotations should be read.
     * @param string $annotationName The name of the annotation.
     *
     * @return object|null The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getClassAnnotation(ReflectionClass $class, string $annotationName);

    /**
     * Gets the annotations applied to a method.
     *
     * @param \ReflectionMethod $method The ReflectionMethod of the method from which
     *                                  the annotations should be read.
     *
     * @return array An array of Annotations.
     */
    public function getMethodAnnotations(ReflectionMethod $method): array;

    /**
     * Gets a method annotation.
     *
     * @param \ReflectionMethod $method The ReflectionMethod to read the annotations from.
     * @param string $annotationName The name of the annotation.
     *
     * @return object|null The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getMethodAnnotation(ReflectionMethod $method, string $annotationName);

    /**
     * Gets the annotations applied to a property.
     *
     * @param \ReflectionProperty $property The ReflectionProperty of the property
     *                                      from which the annotations should be read.
     *
     * @return array An array of Annotations.
     */
    public function getPropertyAnnotations(ReflectionProperty $property): array;

    /**
     * Gets a property annotation.
     *
     * @param \ReflectionProperty $property The ReflectionProperty to read the annotations from.
     * @param string $annotationName The name of the annotation.
     *
     * @return object|null The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getPropertyAnnotation(ReflectionProperty $property, string $annotationName);
}