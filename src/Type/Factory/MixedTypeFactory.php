<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\Type;
use phpDocumentor\Reflection\Type as PhpDocType;

class MixedTypeFactory implements TypeFactory
{
    public function create(PhpDocType $typeHint, array $docTypes, bool $nullable): Type
    {
        return new MixedType();
    }
}
