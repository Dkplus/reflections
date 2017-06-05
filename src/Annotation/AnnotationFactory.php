<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation;

use Dkplus\Reflection\AnnotationReflection;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\ReflectorStrategy;

final class AnnotationFactory
{
    /** @var ReflectorStrategy\ */
    private $reflector;

    public function __construct(ReflectorStrategy $reflector)
    {
        $this->reflector = $reflector;
    }

    public function createReflection(string $identifier, array $values, Context $context): AnnotationReflection
    {
        $class = null;
        foreach ($context->imports() as $alias => $fqcn) {
            if ($alias === $identifier) {
                $identifier = $fqcn;
                $class = $this->reflector->reflectClass($fqcn);
            }
        }
        if (! $class) {
            try {
                $class = $this->reflector->reflectClass($identifier);
            } catch (ClassNotFound $exception) {
            }
        }
        if ($class) {
            return AnnotationReflection::fullyQualified($identifier, $values, $class);
        }
        return AnnotationReflection::unqualified($identifier, $values);
    }
}
