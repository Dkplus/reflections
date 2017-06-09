<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Mixed;

class NullableTypeFactory implements TypeFactory
{
    /** @var TypeFactory */
    private $decorated;

    public function __construct(TypeFactory $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(PhpDocType $typeHint, PhpDocType $docType): Type
    {
        if ($typeHint instanceof Mixed && count($docTypes) > 1 && in_array('null', $docTypes)) {
            $nullable = true;
            unset($docTypes[array_search('null', $docTypes)]);
            $docTypes = array_values($docTypes);
        }
        $result = $this->decorated->create($typeHint, $docTypes, false);
        if ($nullable
            && ! ($result instanceof MixedType || $result instanceof VoidType || $result instanceof NullType)
        ) {
            $result = new NullableType($result);
        }
        return $result;
    }
}
