<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Exception;

use BadMethodCallException;

final class EmptyClasses extends BadMethodCallException
{
    public static function butFirstOneHasBeenRetrieved(): self
    {
        return new self('There are no classes but the first one has been retrieved');
    }
}
