<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassReflector;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Mixed;
use Traversable;

class PhpDocTypeFactory implements TypeFactory
{
    /** @var TypeFactory */
    private $decorated;

    /** @var ClassReflector */
    private $classReflector;

    public function __construct(TypeFactory $decorated, ClassReflector $classReflector)
    {
        $this->decorated = $decorated;
        $this->classReflector = $classReflector;
    }

    public function create(PhpDocType $type, array $docTypes, bool $nullable): Type
    {
        if (count($docTypes) > 1 || substr((string) current($docTypes), -2) === '[]') {
            $nonTraversableDocTypes = array_filter($docTypes, function (string $type) {
                return substr($type, -2) !== '[]';
            });
            $traversableDocTypes = array_diff($docTypes, $nonTraversableDocTypes);

            if (count($traversableDocTypes) > 0 && count($nonTraversableDocTypes) <= 1) {
                $traversableTypes = array_map(
                    function (string $traversablePhpDocType) {
                        return $this->create(new Mixed(), [substr($traversablePhpDocType, 0, -2)], false);
                    },
                    $traversableDocTypes
                );
                $decoratedType = count($traversableTypes) > 1
                    ? new ComposedType(...$traversableTypes)
                    : current($traversableTypes);
                if (count($nonTraversableDocTypes) === 0) {
                    return new IterableType($decoratedType);
                }
                if (current($nonTraversableDocTypes) === 'array') {
                    return new ArrayType($decoratedType);
                }

                $nonTraversableType = $this->create(new Mixed(), $nonTraversableDocTypes, $nullable);
                if ($nonTraversableType instanceof ClassType
                    && $nonTraversableType->implementsOrIsSubClassOf(Traversable::class)
                ) {
                    return new CollectionType($nonTraversableType, $decoratedType);
                }
            }

            return new ComposedType(...array_map(function (string $phpDocType) use ($type, $nullable) {
                return $this->create($type, [$phpDocType], $nullable);
            }, $docTypes));
        }
        if (count($docTypes) === 1) {
            switch (current($docTypes)) {
                case 'string':
                    return new StringType();
                case 'integer':
                case 'int':
                    return new IntegerType();
                case 'float':
                case 'double':
                    return new FloatType();
                case 'boolean':
                case 'bool':
                case 'Bool':
                    return new BooleanType();
                case 'callable':
                case 'callback':
                    return new CallableType();
                case 'resource':
                    return new ResourceType();
                case 'object':
                    return new ObjectType();
                case 'void':
                    return new VoidType();
                case 'array':
                    return new ArrayType();
                case 'iterable':
                    return new IterableType();
                default:
                    return new ClassType($this->classReflector->reflect(current($docTypes)));
            }
        }
        return $this->decorated->create($type, $docTypes, $nullable);
    }
}
