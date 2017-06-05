<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

final class InvalidAnnotationException extends AnnotationException
{
    /**
     * @param string $className
     * @param string $originalName
     * @param string $context
     *
     * @return InvalidAnnotationException
     */
    public static function notAnnotationException(string $className, string $originalName, string $context) : self
    {
        return new self(sprintf(
            'The class "%s" is not annotated with @Annotation. ' .
            'Are you sure this class can be used as annotation? ' .
            'If so, then you need to add @Annotation to the _class_ doc comment of "%s". ' .
            'If it is indeed no annotation, then you need to add @IgnoreAnnotation("%s") to the _class_ doc comment of %s.',
            $className,
            $className,
            $originalName,
            $context
        ));
    }
}
