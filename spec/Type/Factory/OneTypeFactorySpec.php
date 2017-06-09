<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\Factory\OneTypeFactory;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
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
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\Void_;
use PhpSpec\ObjectBehavior;

class OneTypeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OneTypeFactory::class);
    }

    function it_converts_a_Mixed_into_a_MixedType()
    {
        $this->convert(new Mixed())->shouldBeLike(new MixedType());
    }

    function it_converts_a_String_into_a_StringType()
    {
        $this->convert(new String_())->shouldBeLike(new StringType());
    }

    function it_converts_an_Integer_into_an_IntegerType()
    {
        $this->convert(new Integer())->shouldBeLike(new IntegerType());
    }

    function it_converts_a_Float_into_an_FloatType()
    {
        $this->convert(new Float_())->shouldBeLike(new FloatType());
    }

    function it_converts_a_Boolean_into_a_BooleanType()
    {
        $this->convert(new Boolean())->shouldBeLike(new BooleanType());
    }

    function it_converts_an_Object_into_an_ObjectType()
    {
        $this->convert(new Object_())->shouldBeLike(new ObjectType());
    }

    function it_converts_an_Resource_into_a_ResourceType()
    {
        $this->convert(new Resource())->shouldBeLike(new ResourceType());
    }

    function it_converts_a_Callable_into_a_CallableType()
    {
        $this->convert(new Callable_())->shouldBeLike(new CallableType());
    }

    function it_converts_an_Iterable_into_an_IterableType()
    {
        $this->convert(new Iterable_())->shouldBeLike(new IterableType());
    }

    function it_converts_a_Null_into_an_NullType()
    {
        $this->convert(new Null_())->shouldBeLike(new NullType());
    }

    function it_converts_a_Parent_into_a_ClassType()
    {
        $this->convert(new Parent_())->shouldBeLike(new ClassType());
    }

    function it_converts_a_This_into_a_ClassType()
    {
        $this->convert(new This())->shouldBeLike(new ClassType());
    }

    function it_converts_a_Self_into_a_ClassType()
    {
        $this->convert(new Self_())->shouldBeLike(new ClassType());
    }

    function it_converts_a_Scalar_into_a_ScalarType()
    {
        $this->convert(new Scalar())->shouldBeLike(new ComposedType());
    }

    function it_converts_a_Void_into_an_VoidType()
    {
        $this->convert(new Void_())->shouldBeLike(new VoidType());
    }

    function it_converts_an_Array_into_an_ArrayType_of_MixedType()
    {
        $this->convert(new Array_())->shouldBeLike(new ArrayType(new MixedType()));
    }

    function it_converts_a_Class_into_a_ClassType()
    {
        $this
            ->convert(new Object_(new Fqsen('\\stdClass')))
            ->shouldBeLike(new ClassType(new ReflectionClass('stdClass')));
    }

    function it_converts_a_Nullable_into_a_NullableType()
    {
        $this->convert(new Nullable(new String_()))->shouldBeLike(new NullableType(new StringType()));
    }
}
