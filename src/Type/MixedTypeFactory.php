<?php
namespace Dkplus\Reflections\Type;

use phpDocumentor\Reflection\Type as PhpDocumentorType;

class MixedTypeFactory implements TypeFactory
{
    public function create(PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
    {
        return new MixedType();
    }
}
