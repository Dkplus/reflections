<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\Exception;

use RuntimeException;

final class ParserException extends RuntimeException
{
    public static function invalidConstant(string $identifier): self
    {
        return new self(sprintf("Couldn't find constant %s.", $identifier));
    }
}
