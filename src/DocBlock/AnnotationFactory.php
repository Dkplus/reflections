<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use InvalidArgumentException;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
use ReflectionException;

/** @internal */
final class AnnotationFactory
{
    /** @var ClassReflector */
    private $classReflector;

    /** @var FqsenResolver */
    private $fqsenResolver;

    /** @var MultiTagAttributeFormatter */
    private $attributeFormatter;
    /**
     * @var DocBlockReflector
     */
    private $docBlockReflector;

    public function __construct(
        DocBlockReflector $docBlockReflector,
        ClassReflector $classReflector,
        FqsenResolver $fqsenResolver,
        MultiTagAttributeFormatter $attributeFormatter = null
    ) {
        $this->classReflector = $classReflector;
        $this->docBlockReflector = $docBlockReflector;
        $this->fqsenResolver = $fqsenResolver;
        $this->attributeFormatter = $attributeFormatter ?? MultiTagAttributeFormatter::forDefaultTags();
    }

    public function createReflection(string $tag, array $attributes, Context $context): AnnotationReflection
    {
        try {
            $annotationClassName = (string) $this->fqsenResolver->resolve($tag, $context);
            $reflection = $this->classReflector->reflect($annotationClassName); // check whether fully qualified
            $inherited = $this->reflectAnnotationsFromClass($reflection);
            return AnnotationReflection::fullyQualified($annotationClassName, $attributes, ...$inherited);
        } catch (InvalidArgumentException $exception) {
        } catch (ReflectionException $exception) {
        }
        $attributes = $this->attributeFormatter->format($tag, $attributes, $context);
        return AnnotationReflection::unqualified($tag, $attributes);
    }

    private function reflectAnnotationsFromClass(ReflectionClass $class): Annotations
    {
        $annotationsDocBlock = $this->docBlockReflector->reflectDocBlock(
            (string) $class->getDocComment(),
            (new ContextFactory())->createFromReflector($class)
        );
        return $annotationsDocBlock->annotations();
    }
}
