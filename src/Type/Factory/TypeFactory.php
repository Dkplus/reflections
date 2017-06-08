<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\Type;
use phpDocumentor\Reflection\Type as PhpDocType;

interface TypeFactory
{
    /**
     * @param PhpDocType $typeHint
     * @param string[] $docTypes
     * @param bool $nullable
     * @return Type
     */
    public function create(PhpDocType $typeHint, array $docTypes, bool $nullable): Type;
}
