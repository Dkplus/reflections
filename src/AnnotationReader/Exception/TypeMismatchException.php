<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

final class TypeMismatchException extends AnnotationException
{

    /**
     * Creates a new AnnotationException describing a invalid enummerator.
     *
     * @param string $attributeName
     * @param string $annotationName
     * @param string $context
     * @param array  $available
     * @param mixed  $given
     *
     * @return AnnotationException
     */
    public static function enumeratorError($attributeName, $annotationName, $context, $available, $given) : self
    {
        $format  = 'Attribute "%s" of @%s declared on %s accept only [%s], but got %s.';
        $options = implode(', ', $available);
        $label   = is_object($given)
            ? get_class($given)
            : $given;
        return new self(sprintf(
            $format,
            $attributeName,
            $annotationName,
            $context,
            $options,
            $label
        ));
    }
    /**
     * Creates a new AnnotationException describing an type error of an attribute.
     *
     * @param string $attributeName
     * @param string $annotationName
     * @param string $context
     * @param string $expected
     * @param mixed  $actual
     *
     * @return AnnotationException
     */
    public static function attributeTypeError(string $attributeName, string $annotationName, string $context, string $expected, $actual) : self
    {
        $format = 'Attribute "%s" of @%s declared on %s expects %s, but got %s.';
        $label  = is_object($actual)
            ? 'an instance of ' . get_class($actual)
            : gettype($actual);
        return new self(sprintf(
            $format,
            $attributeName,
            $annotationName,
            $context,
            $expected,
            $label
        ));
    }
}
