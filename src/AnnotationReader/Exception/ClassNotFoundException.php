<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

final class ClassNotFoundException extends AnnotationException
{
    /**
     * @param string $className
     * @param string $context
     *
     * @return ClassNotFoundException
     */
    public static function annotationNotFound($className, $context): self
    {
        return new self(sprintf(
            'The annotation "@%s" in %s does not exist, or could not be auto-loaded.',
            $className,
            $context
        ));
    }

    /**
     * @param string $className
     * @param string $context
     *
     * @return ClassNotFoundException
     */
    public static function annotationNotImported($className, $context): self
    {
        return new self(sprintf(
            'The annotation "@%s" in %s was never imported. ' .
            'Did you maybe forget to add a "use" statement for this annotation ?',
            $className,
            $context
        ));
    }
}
