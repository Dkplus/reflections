<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use Hoa\Compiler\Exception as HoaException;
use RuntimeException;

final class ParserException extends RuntimeException
{

    /**
     * @param \Hoa\Compiler\Exception $exception
     * @param string $context
     *
     * @return ParserException
     */
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
