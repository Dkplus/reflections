<?php

namespace Dkplus\Reflections;

use Dkplus\Reflections\Type\MixedTypeFactory;
use Dkplus\Reflections\Type\NullableTypeFactory;
use Dkplus\Reflections\Type\PhpDocTypeFactory;
use Dkplus\Reflections\Type\TypeFactory;
use Dkplus\Reflections\Type\TypeHintTypeFactory;

class Builder
{
    public static function create(): Builder
    {
        return new Builder();
    }

    private function __construct()
    {
    }

    public function typeFactory(): TypeFactory
    {
        return new NullableTypeFactory(new TypeHintTypeFactory(new PhpDocTypeFactory(new MixedTypeFactory())));
    }

    public function reflector(TypeFactory $typeFactory): Reflector
    {
        return new AutoloadingReflector($typeFactory);
    }
}
