<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Dkplus\Reflection\ReflectorStrategy;
use phpDocumentor\Reflection\Type as PhpDocumentorType;

class MixedTypeFactory implements TypeFactory
{
    public function create(ReflectorStrategy $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
    {
        return new MixedType();
    }
}
