<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\Resource;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;

class OneTypeFactory
{
    public function convert(PhpDocType $phpDocType): Type
    {
        if ($phpDocType instanceof Nullable) {
            return new NullableType($this->convert($phpDocType->getActualType()));
        }
        if ($phpDocType instanceof Integer) {
            return new IntegerType();
        }
        if ($phpDocType instanceof Float_) {
            return new FloatType();
        }
        if ($phpDocType instanceof String_) {
            return new StringType();
        }
        if ($phpDocType instanceof Boolean) {
            return new BooleanType();
        }
        if ($phpDocType instanceof Object_ && $phpDocType->getFqsen() === null) {
            return new ObjectType();
        }
        if ($phpDocType instanceof Object_) {
            return new ObjectType();
        }
        if ($phpDocType instanceof Callable_) {
            return new CallableType();
        }
        if ($phpDocType instanceof Resource) {
            return new ResourceType();
        }
        if ($phpDocType instanceof Void_) {
            return new VoidType();
        }
        if ($phpDocType instanceof Null_) {
            return new NullType();
        }
        if ($phpDocType instanceof Parent_) {
            return new ObjectType();
        }
        if ($phpDocType instanceof Self_) {
            return new ObjectType();
        }
        if ($phpDocType instanceof Static_) {
            return new ObjectType();
        }
        if ($phpDocType instanceof Array_) {
            return new ArrayType();
        }
        if ($phpDocType instanceof Iterable_) {
            return new IterableType();
        }
        return new MixedType();
    }
}
