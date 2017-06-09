<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\Type;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Mixed;

class MixingTypeFactory
{
    public function create(PhpDocType $typeHint, PhpDocType $docType): Type
    {
        $typeHintIsMixed = $typeHint instanceof Mixed;
        $docTypeIsMixed = $docType instanceof Mixed;
        if ($typeHintIsMixed && $docTypeIsMixed) {
            return new MixedType();
        }
        if (! $typeHintIsMixed) {
            return $this->createFromSingleType($typeHint);
        }
        if (! $docTypeIsMixed) {
            return $this->createFromSingleType($docType);
        }
        if ($typeHint instanceof Mixed && $docType instanceof Mixed) {
            return new MixedType();
        }
        $typeHintType = $this->create($typeHint, new Mixed());
        $docTypeType = $this->create(new Mixed(), $docType);
        if ($typeHintType == $docTypeType) {
            return $typeHintType;
        }
    }

    private function createFromSingleType(PhpDocType $type): Type
    {

    }
}
