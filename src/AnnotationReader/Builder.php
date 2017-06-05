<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use Dkplus\Reflection\AnnotationReader\Exception\TypeMismatchException;
use Dkplus\Reflection\AnnotationReader\Metadata\ClassMetadata;
use Dkplus\Reflection\AnnotationReader\Metadata\MetadataFactory;

final class Builder
{
    /** @var Resolver */
    private $resolver;

    /** MetadataFactory */
    private $metadataFactory;

    public function __construct(Resolver $resolver, MetadataFactory $metadataFactory)
    {
        $this->resolver        = $resolver;
        $this->metadataFactory = $metadataFactory;
    }
    /**
     * @param Context   $context
     * @param Reference $reference
     *
     * @return object
     */
    public function create(Context $context, Reference $reference)
    {
        $target    = $reference->nested ? Target::TARGET_ANNOTATION : $context->getTarget();
        $fullClass = $this->resolver->resolve($context, $reference->name);
        $metadata  = $this->metadataFactory->getMetadataFor($fullClass);
        $values    = $reference->values;
        if ($metadata === null) {
            throw InvalidAnnotationException::notAnnotationException($fullClass, $reference->name, $context->getDescription());
        }
        if (($metadata->target & $target) === 0) {
            $contextDesc   = $context->getDescription();
            $allowedTarget = implode(',', Target::getNames($metadata->target));
            throw TargetNotAllowedException::notAllowedDeclaration($fullClass, $contextDesc, $allowedTarget);
        }
        return $this->instantiate($context, $metadata, $values);
    }
    /**
     * @param Context       $context
     * @param ClassMetadata $metadata
     * @param array         $values
     *
     * @return object
     */
    private function instantiate(Context $context, ClassMetadata $metadata, array $values)
    {
        $this->assertPropertyTypes($context, $metadata, $values);
        $className  = $metadata->class;
        $annotation = $metadata->hasConstructor
            ? new $className($values)
            : new $className();
        if ( ! $metadata->hasConstructor) {
            $this->injectValues($annotation, $context, $metadata, $values);
        }
        return $annotation;
    }
    /**
     * @param object        $annotation
     * @param Context       $context
     * @param ClassMetadata $metadata
     * @param array         $values
     */
    private function injectValues($annotation, Context $context, ClassMetadata $metadata, array $values)
    {
        $properties      = $metadata->properties;
        $defaultProperty = $metadata->defaultProperty;
        $propertyNames   = array_keys($metadata->properties);
        foreach ($values as $property => $value) {
            if (isset($properties[$property])) {
                $annotation->{$property} = $value;
                continue;
            }
            if ($property !== 'value') {
                throw new \RuntimeException(sprintf(
                    'The annotation @%s declared on %s does not have a property named "%s". Available properties: %s',
                    $metadata->class,
                    $context->getDescription(),
                    $property,
                    implode(', ', $propertyNames)
                ));
            }
            // handle the case if the property has no annotations
            if ( ! $defaultProperty) {
                throw new \RuntimeException(sprintf(
                    'The annotation @%s declared on %s does not accept any values, but got %s.',
                    $metadata->class,
                    $context->getDescription(),
                    json_encode($values)
                ));
            }
            $annotation->{$defaultProperty} = $value;
        }
    }
    /**
     * @param Context       $context
     * @param ClassMetadata $metadata
     * @param array         $values
     *
     * @return object
     */
    private function assertPropertyTypes(Context $context, ClassMetadata $metadata, array $values)
    {
        $properties      = $metadata->properties;
        $defaultProperty = $metadata->defaultProperty;
        // checks all declared attributes
        foreach ($metadata->properties as $propertyName => $property) {
            if ($propertyName === $defaultProperty && ! isset($values[$propertyName]) && isset($values['value'])) {
                $propertyName = 'value';
            }
            // handle a not given attribute
            if ( ! isset($values[$propertyName]) && $property['required']) {
                throw AnnotationException::requiredError($propertyName, $metadata->class, $this->context, 'a(n) '.$property['value']);
            }
            // null values
            if ( ! isset($values[$propertyName])) {
                continue;
            }
            // checks if the attribute is a valid enumerator
            if (isset($property['enum']) && ! in_array($values[$propertyName], $property['enum']['value'])) {
                throw TypeMismatchException::enumeratorError(
                    $propertyName,
                    $metadata->class,
                    $context->getDescription(),
                    $property['enum']['literal'],
                    $values[$propertyName]
                );
            }
            // mixed values
            if ( ! isset($property['type']) || $property['type'] === 'mixed') {
                continue;
            }
            if ($property['type'] === 'array') {
                // handle the case of a single value
                if ( ! is_array($values[$propertyName])) {
                    $values[$propertyName] = array($values[$propertyName]);
                }
                // checks if the attribute has array type declaration, such as "array<string>"
                if ( ! isset($property['array_type'])) {
                    continue;
                }
                foreach ($values[$propertyName] as $item) {
                    if (gettype($item) !== $property['array_type'] && ! $item instanceof $property['array_type']) {
                        throw TypeMismatchException::attributeTypeError(
                            $propertyName,
                            $metadata->class,
                            $context->getDescription(),
                            'either a(n) ' . $property['array_type'] . ', or an array of ' . $property['array_type'] . 's',
                            $item
                        );
                    }
                }
                continue;
            }
            if (gettype($values[$propertyName]) !== $property['type'] && ! $values[$propertyName] instanceof $property['type']) {
                throw TypeMismatchException::attributeTypeError(
                    $propertyName,
                    $metadata->class,
                    $context->getDescription(),
                    'a(n) ' . $property['type'],
                    $values[$propertyName]
                );
            }
        }
    }
}
