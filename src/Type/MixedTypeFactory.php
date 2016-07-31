<?php
namespace Dkplus\Reflections\Type;

use Dkplus\Reflections\Reflector;
use phpDocumentor\Reflection\Type as PhpDocumentorType;

class MixedTypeFactory implements TypeFactory
{
    public function create(Reflector $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
    {
        return new MixedType();
    }
}
