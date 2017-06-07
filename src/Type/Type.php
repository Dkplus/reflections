<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

interface Type
{
    public function accepts(Type $type): bool;
    public function __toString(): string;
}
