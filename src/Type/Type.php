<?php
namespace Dkplus\Reflections\Type;

interface Type
{
    public function allows(Type $type): bool;
    public function __toString(): String;
}
