<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use function array_diff;
use function array_filter;
use function array_map;
use function array_walk;
use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassReflector;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\ScalarType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use function explode;
use function in_array;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\Resource;
use phpDocumentor\Reflection\Types\Scalar;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\Void_;

class TypeConverter
{
    /** @var ClassReflector */
    private $reflector;

    public function __construct(ClassReflector $reflector)
    {
        $this->reflector = $reflector;
    }

    public function convert(PhpDocType $phpDocType, Fqsen $context): Type
    {
        if ($phpDocType instanceof Compound) {
            $converted = [];
            $index = 0;
            while ($phpDocType->has($index)) {
                $converted[] = $this->convert($phpDocType->get($index), $context);
                ++$index;
            }
            return $this->createFromTypes(...$converted);
        }
        if ($phpDocType instanceof Nullable) {
            return new NullableType($this->convert($phpDocType->getActualType(), $context));
        }
        if ($phpDocType instanceof Scalar) {
            return new ScalarType();
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
        if ($phpDocType instanceof This) {
            return new ClassType($this->reflectClass($context));
        }
        if ($phpDocType instanceof Object_) {
            return $phpDocType->getFqsen() === null
                ? new ObjectType()
                : new ClassType($this->reflector->reflect((string) $phpDocType->getFqsen()));
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
            return new ClassType($this->reflectClass($context)->getParentClass());
        }
        if ($phpDocType instanceof Self_) {
            return new ClassType($this->findDeclaringClass($context));
        }
        if ($phpDocType instanceof Static_) {
            return new ClassType($this->reflectClass($context));
        }
        if ($phpDocType instanceof Array_) {
            return $phpDocType->getValueType() instanceof Mixed
                ? new ArrayType()
                : new IterableType($this->convert($phpDocType->getValueType(), $context));
        }
        if ($phpDocType instanceof Iterable_) {
            return new IterableType();
        }
        return new MixedType();
    }

    private function createFromTypes(Type ...$types): Type
    {
        $type = $this->flatten(...$types);
        $isNullable = false;
        if ($type instanceof NullableType) {
            $isNullable = true;
            $type = $type->decoratedType();
        }

        if ($type instanceof ComposedType) {
            $type = $this->compose(...$type->decoratedTypes());
        }
        if ($isNullable) {
            $type = new NullableType($type);
        }
        return $type;
    }

    private function compose(Type ...$types)
    {
        if (count($types) === 4
            && in_array(new IntegerType(), $types)
            && in_array(new FloatType(), $types)
            && in_array(new StringType(), $types)
            && in_array(new BooleanType(), $types)
        ) {
            return new ScalarType();
        }
        if (in_array(new ArrayType(), $types)) {
            $arrays = array_filter($types, function (Type $type) {
                return $type instanceof ArrayType
                    && $type->decoratedType() instanceof Mixed;
            });
            $iterableTypes = array_filter($types, function (Type $type) {
                return $type instanceof IterableType
                    && ! $type->decoratedType() instanceof Mixed;
            });
            if (count($iterableTypes) > 0) {
                $type = new ArrayType($this->createFromTypes(...array_map(function (IterableType $type) {
                    return $type->decoratedType();
                }, $iterableTypes)));
                return $this->createFromTypes($type, ...array_diff($types, $iterableTypes, $arrays));
            }
        }
        return new ComposedType(...$types);
    }

    private function flatten(Type ...$types): Type
    {
        $isNullable = false;
        $types = array_merge([], ...array_map(function (Type $type) use (&$isNullable) {
            if ($type instanceof NullableType) {
                $isNullable = true;
                $type = $type->decoratedType();
            }
            if ($type instanceof NullType) {
                $isNullable = true;
                return [];
            }
            if ($type instanceof ComposedType) {
                return $type->decoratedTypes();
            }
            return [$type];
        }, $types));
        $result = new MixedType();
        if (count($types) > 0) {
            $result = count($types) > 1
                ? new ComposedType(...$types)
                : current($types);
        }
        if ($isNullable) {
            $result = new NullableType($result);
        }
        return $result;
    }

    private function findDeclaringClass(Fqsen $context): ReflectionClass
    {
        $class = $this->reflectClass($context);
        if ($this->isMethod($context)) {
            while ($class->hasMethod($context->getName()) && $class->getParentClass()) {
                $class = $class->getParentClass();
            }
            return $class;
        }
        if ($this->isProperty($context)) {
            while ($class->hasProperty($context->getName()) && $class->getParentClass()) {
                $class = $class->getParentClass();
            }
            return $class;
        }
        return $class;
    }

    private function reflectClass(Fqsen $context): ReflectionClass
    {
        return $this->reflector->reflect(explode('::', (string) $context)[0]);
    }

    private function isMethod(Fqsen $context): bool
    {
        return substr((string) $context, -2) === '()';
    }

    private function isProperty(Fqsen $context): bool
    {
        $parts = explode('::', (string) $context);
        return substr(end($parts), 0, 1) === '$';
    }
}
