<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassReflector;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\Factory\TypeConverter;
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
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Fqsen;
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
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\Void_;
use PhpSpec\ObjectBehavior;
use ReflectionClass;
use test\Dkplus\Reflection\Fixtures\ClassWithInterface;
use test\Dkplus\Reflection\Fixtures\ClassWithParent;
use test\Dkplus\Reflection\Fixtures\StaticSelfClass;

class TypeConverterSpec extends ObjectBehavior
{
    function let(ClassReflector $reflector)
    {
        $this->beConstructedWith($reflector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeConverter::class);
    }

    function it_converts_a_Mixed_into_a_MixedType()
    {
        $this->convert(new Mixed(), new Fqsen('\\MyClass'))->shouldBeLike(new MixedType());
    }

    function it_converts_a_String_into_a_StringType()
    {
        $this->convert(new String_(), new Fqsen('\\MyClass'))->shouldBeLike(new StringType());
    }

    function it_converts_an_Integer_into_an_IntegerType()
    {
        $this->convert(new Integer(), new Fqsen('\\MyClass'))->shouldBeLike(new IntegerType());
    }

    function it_converts_a_Float_into_an_FloatType()
    {
        $this->convert(new Float_(), new Fqsen('\\MyClass'))->shouldBeLike(new FloatType());
    }

    function it_converts_a_Boolean_into_a_BooleanType()
    {
        $this->convert(new Boolean(), new Fqsen('\\MyClass'))->shouldBeLike(new BooleanType());
    }

    function it_converts_an_Object_into_an_ObjectType()
    {
        $this->convert(new Object_(), new Fqsen('\\MyClass'))->shouldBeLike(new ObjectType());
    }

    function it_converts_an_Resource_into_a_ResourceType()
    {
        $this->convert(new Resource(), new Fqsen('\\MyClass'))->shouldBeLike(new ResourceType());
    }

    function it_converts_a_Callable_into_a_CallableType()
    {
        $this->convert(new Callable_(), new Fqsen('\\MyClass'))->shouldBeLike(new CallableType());
    }

    function it_converts_an_Iterable_into_an_IterableType()
    {
        $this->convert(new Iterable_(), new Fqsen('\\MyClass'))->shouldBeLike(new IterableType());
    }

    function it_converts_a_Null_into_an_NullType()
    {
        $this->convert(new Null_(), new Fqsen('\\MyClass'))->shouldBeLike(new NullType());
    }

    function it_converts_a_Parent_into_a_ClassType(ClassReflector $reflector)
    {
        $reflector
            ->reflect('\\' . ClassWithParent::class)
            ->willReturn(new ReflectionClass(ClassWithParent::class));

        $this
            ->convert(new Parent_(), new Fqsen('\\' . ClassWithParent::class))
            ->shouldBeLike(new ClassType(new ReflectionClass(ClassWithInterface::class)));
    }

    function it_converts_a_This_into_a_ClassType(ClassReflector $reflector)
    {
        $stdReflection = new ReflectionClass('\\stdClass');
        $reflector->reflect('\\stdClass')->willReturn($stdReflection);
        $this->convert(new This(), new Fqsen('\\stdClass'))->shouldBeLike(new ClassType($stdReflection));
    }

    function it_converts_a_Self_into_a_ClassType(ClassReflector $reflector)
    {
        $reflection = new ReflectionClass(ClassWithParent::class);
        $reflector->reflect('\\' . ClassWithParent::class)->willReturn($reflection);

        $this
            ->convert(new Self_(), new Fqsen('\\' . ClassWithParent::class))
            ->shouldBeLike(new ClassType($reflection));
    }

    function it_considers_the_declaring_class_when_converting_Self(ClassReflector $reflector)
    {
        $reflection = new ReflectionClass(StaticSelfClass::class);
        $reflector->reflect('\\' . StaticSelfClass::class)->willReturn($reflection);

        $this
            ->convert(new Self_(), new Fqsen('\\' . StaticSelfClass::class . '::selfReturn()'))
            ->shouldBeLike(new ClassType($reflection->getParentClass()));
    }

    function it_converts_a_Static_into_a_ClassType(ClassReflector $reflector)
    {
        $reflection = new ReflectionClass(ClassWithParent::class);
        $reflector->reflect('\\' . ClassWithParent::class)->willReturn($reflection);

        $this
            ->convert(new Static_(), new Fqsen('\\' . ClassWithParent::class))
            ->shouldBeLike(new ClassType($reflection));
    }

    function it_does_not_consider_the_declaring_class_when_converting_Static(ClassReflector $reflector)
    {
        $reflection = new ReflectionClass(StaticSelfClass::class);
        $reflector->reflect('\\' . StaticSelfClass::class)->willReturn($reflection);

        $this
            ->convert(new Static_(), new Fqsen('\\' . StaticSelfClass::class . '::staticReturn()'))
            ->shouldBeLike(new ClassType($reflection));
    }

    function it_converts_a_Scalar_into_a_ScalarType()
    {
        $this->convert(new Scalar(), new Fqsen('\\stdClass'))->shouldBeLike(new ScalarType());
    }

    function it_converts_a_Void_into_an_VoidType()
    {
        $this->convert(new Void_(), new Fqsen('\\MyClass'))->shouldBeLike(new VoidType());
    }

    function it_converts_an_Array_into_an_ArrayType_of_MixedType()
    {
        $this->convert(new Array_(), new Fqsen('\\MyClass'))->shouldBeLike(new ArrayType(new MixedType()));
    }

    function it_converts_an_Array_of_a_specific_Type_into_an_IterableType_of_this_Type()
    {
        $this
            ->convert(new Array_(new String_()), new Fqsen('\\MyClass'))
            ->shouldBeLike(new IterableType(new StringType()));
    }

    function it_converts_an_Object_with_a_Fqsen_into_a_ClassType(ClassReflector $reflector)
    {
        $reflector->reflect('\\stdClass')->willReturn(new ReflectionClass('\\stdClass'));
        $this
            ->convert(new Object_(new Fqsen('\\stdClass')), new Fqsen('\\MyClass'))
            ->shouldBeLike(new ClassType(new ReflectionClass('stdClass')));
    }

    function it_converts_a_Nullable_into_a_NullableType()
    {
        $this
            ->convert(new Nullable(new String_()), new Fqsen('\\MyClass'))
            ->shouldBeLike(new NullableType(new StringType()));
    }

    function it_converts_a_Compound_of_multiple_Types_to_a_ComposedType()
    {
        $this
            ->convert(new Compound([new Integer(), new Float_()]), new Fqsen('\\MyClass'))
            ->shouldBeLike(new ComposedType(new IntegerType(), new FloatType()));
    }

    function it_converts_a_Compound_of_no_Types_to_a_MixedType()
    {
        $this
            ->convert(new Compound([]), new Fqsen('\\MyClass'))
            ->shouldBeLike(new MixedType());
    }

    function it_converts_a_Compound_of_one_Type_by_converting_only_the_Type()
    {
        $this
            ->convert(new Compound([new String_()]), new Fqsen('\\MyClass'))
            ->shouldBeLike(new StringType());
    }

    function it_converts_a_Compound_of_Float_Integer_Boolean_and_String_as_ScalarType()
    {
        $this->convert(
            new Compound([new Float_(), new Integer(), new Boolean(), new String_()]),
            new Fqsen('\\MyClass')
        )->shouldBeLike(new ScalarType());
    }

    function it_flattens_multiple_Compounds_while_converting()
    {
        $this->convert(
            new Compound([new Float_(), new Compound([new Integer(), new Boolean()])]),
            new Fqsen('\\MyClass')
        )->shouldBeLike(new ComposedType(new FloatType(), new IntegerType(), new BooleanType()));
    }

    function it_converts_a_Compound_with_one_Nullable_into_a_Nullable_ComposedType()
    {
        $this->convert(
            new Compound([new Integer(), new Nullable(new Float_())]),
            new Fqsen('\\MyClass')
        )->shouldBeLike(new NullableType(new ComposedType(new IntegerType(), new FloatType())));
    }

    function it_converts_a_Compound_with_one_Null_into_a_Nullable()
    {
        $this->convert(
            new Compound([new String_(), new Null_()]),
            new Fqsen('\\MyClass')
        )->shouldBeLike(new NullableType(new StringType()));
    }

    function it_converts_a_Compound_of_one_Array_and_an_Array_of_String_to_a_ArrayType_of_StringType()
    {
        $this
            ->convert(new Compound([new Array_(), new Array_(new String_())]), new Fqsen('\\MyClass'))
            ->shouldBeLike(new ArrayType(new StringType()));
    }

    function it_converts_a_Compound_of_one_traversable_ClassType_with_an_IterableType_to_a_CollectionType(
        ClassReflector $reflector
    ) {
        $iterable = new ReflectionClass('\\ArrayObject');
        $reflector->reflect('\\ArrayObject')->willReturn($iterable);

        $this->convert(
            new Compound([new Object_(new Fqsen('\\ArrayObject')), new Array_(new String_())]),
            new Fqsen('\\MyClass')
        )->shouldBeLike(new CollectionType(new ClassType($iterable), new StringType()));
    }
}
