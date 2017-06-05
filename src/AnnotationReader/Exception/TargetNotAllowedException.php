<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

final class TargetNotAllowedException extends AnnotationException
{
    public static function notAllowedDeclaration(string $className, string $context, string $allowed): self
    {
        return new self(sprintf(
            'Annotation @%s is not allowed to be declared on %s. ' .
            'You may only use this annotation on these code elements: %s.',
            $className,
            $context,
            $allowed
        ));
    }
}
