<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Dkplus\Reflection\ReflectorStrategy;
use phpDocumentor\Reflection\Type as PhpDocumentorType;
use phpDocumentor\Reflection\Types\Mixed;

class NullableTypeFactory implements TypeFactory
{
    /** @var TypeFactory */
    private $decorated;

    public function __construct(TypeFactory $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(ReflectorStrategy $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
    {
        if ($type instanceof Mixed && count($phpDocTypes) > 1 && in_array('null', $phpDocTypes)) {
            $nullable = true;
            unset($phpDocTypes[array_search('null', $phpDocTypes)]);
            $phpDocTypes = array_values($phpDocTypes);
        }
        $result = $this->decorated->create($reflector, $type, $phpDocTypes, false);
        if ($nullable
            && ! ($result instanceof MixedType || $result instanceof VoidType || $result instanceof NullType)
        ) {
            $result = new NullableType($result);
        }
        return $result;
    }
}
