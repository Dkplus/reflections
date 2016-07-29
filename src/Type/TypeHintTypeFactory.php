<?php
namespace Dkplus\Reflections\Type;

use phpDocumentor\Reflection\Type as PhpDocumentorType;
use Dkplus\Reflections\Reflector;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void;

class TypeHintTypeFactory implements TypeFactory
{
    /** @var Reflector */
    private $reflector;

    /** @var TypeFactory */
    private $decorated;

    public function __construct(Reflector $reflector, TypeFactory $decorated)
    {
        $this->reflector = $reflector;
        $this->decorated = $decorated;
    }

    public function create(PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
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
        if ($type instanceof Void) {
            return new VoidType();
        }
        if ($type instanceof Array_) {
            return $this->decorated->create($type, array_unique(array_merge($phpDocTypes, ['array'])), $nullable);
        }
        if (! $type instanceof Object_) {
            return $this->decorated->create($type, $phpDocTypes, $nullable);
        }
        if ($type->getFqsen() === null) {
            return new ObjectType();
        }
        return $this->decorated->create(
            $type,
            array_unique(array_merge($phpDocTypes, [$type->getFqsen()->getName()])),
            $nullable
        );
    }
}
