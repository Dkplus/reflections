<?php
namespace Dkplus\Reflections\Type;

use phpDocumentor\Reflection\Type as PhpDocumentorType;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use Dkplus\Reflections\Reflector;
use phpDocumentor\Reflection\Types\Void;

class TypeFactory
{
    /** @var Reflector */
    private $reflector;

    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    /**
     * [ ] array
     * [✔] boolean
     * [✔] callable
     * [✔✘] class
     * [ ] composed
     * [✔] float
     * [✔] integer
     * [ ] iterable
     * [✔] mixed
     * [ ] nullable
     * [✔] null
     * [✔] object
     * [✔] resource
     * [✔] string
     * [✔] void
     */
    public function create(PhpDocumentorType $type = null, array $phpDocTypes = []): Type
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
        if ($type instanceof Object_) {
            if ($type->getFqsen() === null) {
                return new ObjectType();
            }
            return new ClassType($this->reflector->reflectClass($type->getFqsen()->getName()));
        }
        if ($type === null && count($phpDocTypes) === 1) {
            switch (current($phpDocTypes)) {
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
            }
        }
        return new MixedType();
    }
}
