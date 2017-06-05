<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

use Hoa\Compiler\Exception as HoaException;

final class ParserException extends AnnotationException
{

    public static function hoaException(HoaException $exception, string $context): self
    {
        $code = $exception->getCode();
        $message = 'Fail to parse ' . $context . PHP_EOL . $exception->getMessage();
        return new self($message, $code, $exception);
    }

    /**
     * Creates a new AnnotationException describing a constant error.
     *
     * @param string $identifier
     *
     * @return ParserException
     */
    public static function invalidConstant(string $identifier): self
    {
        return new self(sprintf("Couldn't find constant %s.", $identifier));
    }

}
