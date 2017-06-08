<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;

class TypeHintTypeFactory implements TypeFactory
{
    /** @var TypeFactory */
    private $decorated;

    public function __construct(TypeFactory $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(PhpDocType $type, array $docTypes, bool $nullable): Type
    {
        if ($type instanceof String_) {
            return new StringType();
        }
        if ($type instanceof Integer) {
            return new IntegerType();
        }
        if ($type instanceof Float_) {
            return new FloatType();
        }
        if ($type instanceof Boolean) {
            return new BooleanType();
        }
        if ($type instanceof Callable_) {
            return new CallableType();
        }
        if ($type instanceof Void_) {
            return new VoidType();
        }
        if ($type instanceof Array_) {
            return $this->decorated->create(
                $type,
                array_unique(array_merge($docTypes, ['array'])),
                $nullable
            );
        }
        if (! $type instanceof Object_) {
            return $this->decorated->create($type, $docTypes, $nullable);
        }
        if ($type->getFqsen() === null) {
            return new ObjectType();
        }
        return $this->decorated->create(
            $type,
            array_unique(array_merge($docTypes, [$type->getFqsen()->getName()])),
            $nullable
        );
    }
}
