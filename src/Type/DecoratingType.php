<?php
namespace Dkplus\Reflections\Type;

interface DecoratingType extends Type
{
    public function decoratedType(): Type;
}
