<?php

namespace Dkplus\Reflection;

use Dkplus\Reflection\Type\MixedTypeFactory;
use Dkplus\Reflection\Type\NullableTypeFactory;
use Dkplus\Reflection\Type\PhpDocTypeFactory;
use Dkplus\Reflection\Type\TypeFactory;
use Dkplus\Reflection\Type\TypeHintTypeFactory;

class Builder
{
    public static function create(): Builder
    {
        return new self();
    }

    private function __construct()
    {
    }

    public function typeFactory(): TypeFactory
    {
        return new NullableTypeFactory(new TypeHintTypeFactory(new PhpDocTypeFactory(new MixedTypeFactory())));
    }

    public function reflector(TypeFactory $typeFactory): ReflectorStrategy
    {
        return new AutoloadingReflectorStrategy($typeFactory);
    }
}
