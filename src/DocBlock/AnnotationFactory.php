<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use InvalidArgumentException;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionException;
use function array_merge;

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
            $fqsen = (string) $this->fqsenResolver->resolve($tag, $context);
            $this->classReflector->reflect($fqsen); // check whether fully qualified
            $providedTags = $this->reflectAnnotationsFromClass($fqsen);
            return AnnotationReflection::fullyQualified($fqsen, $attributes, ...$providedTags);
        } catch (InvalidArgumentException $exception) {
        } catch (ReflectionException $exception) {
        }
        $attributes = $this->attributeFormatter->format($tag, $attributes, $context);
        return AnnotationReflection::unqualified($tag, $attributes);
    }

    private function reflectAnnotationsFromClass(string $className): array
    {
        $annotations = new Annotations();
        $tags = [];
        try {
            $class = $this->classReflector->reflect($className);
            if ($parent = $class->getParentClass()) {
                $tags[] = $parent->getName();
            }

            $annotations->merge($this->docBlockReflector->reflectDocBlock(
                $class->getDocComment(),
                (new ContextFactory())->createFromReflector($class)
            )->annotations());

            $tags = array_unique(array_merge($tags, $docBlock->providedTags(), $class->getInterfaceNames()));

        } catch (ReflectionException $exception) {
        }
        return $tags;
    }
}
