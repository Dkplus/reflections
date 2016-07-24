<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Reflector;
use Dkplus\Reflections\Type\BooleanType;
use Dkplus\Reflections\Type\CallableType;
use Dkplus\Reflections\Type\ClassType;
use Dkplus\Reflections\Type\FloatType;
use Dkplus\Reflections\Type\IntegerType;
use Dkplus\Reflections\Type\MixedType;
use Dkplus\Reflections\Type\ObjectType;
use Dkplus\Reflections\Type\ResourceType;
use Dkplus\Reflections\Type\StringType;
use Dkplus\Reflections\Type\TypeFactory;
use Dkplus\Reflections\Type\VoidType;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflections\Mock\ClassReflectionStubBuilder;

/**
 * @mixin TypeFactory
 */
class TypeFactorySpec extends ObjectBehavior
{
    function let(Reflector $reflector)
    {
        $this->beConstructedWith($reflector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeFactory::class);
    }

    function it_creates_a_mixed_type_if_no_information_are_available()
    {
        $this->create(null, [])->shouldBeAnInstanceOf(MixedType::class);
    }

    function it_creates_a_string_type_if_a_string_type_is_given()
    {
        $this->create(new String_(), [])->shouldBeAnInstanceOf(StringType::class);
    }

    function it_creates_a_string_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['string'])->shouldBeAnInstanceOf(StringType::class);
    }

    function it_creates_an_integer_type_if_an_integer_type_is_given()
    {
        $this->create(new Integer(), [])->shouldBeAnInstanceOf(IntegerType::class);
    }

    function it_creates_an_integer_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['integer'])->shouldBeAnInstanceOf(IntegerType::class);
        $this->create(null, ['int'])->shouldBeAnInstanceOf(IntegerType::class);
    }

    function it_creates_a_float_type_if_a_float_type_is_given()
    {
        $this->create(new Float_(), [])->shouldBeAnInstanceOf(FloatType::class);
    }

    function it_creates_a_float_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['float'])->shouldBeAnInstanceOf(FloatType::class);
        $this->create(null, ['double'])->shouldBeAnInstanceOf(FloatType::class);
    }

    function it_creates_a_bool_type_if_a_bool_type_is_given()
    {
        $this->create(new Boolean(), [])->shouldBeAnInstanceOf(BooleanType::class);
    }

    function it_creates_a_bool_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['boolean'])->shouldBeAnInstanceOf(BooleanType::class);
        $this->create(null, ['bool'])->shouldBeAnInstanceOf(BooleanType::class);
        $this->create(null, ['Bool'])->shouldBeAnInstanceOf(BooleanType::class);
    }

    function it_creates_a_callable_if_a_callable_type_is_given()
    {
        $this->create(new Callable_(), [])->shouldBeAnInstanceOf(CallableType::class);
    }

    function it_creates_a_callable_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['callable'])->shouldBeAnInstanceOf(CallableType::class);
        $this->create(null, ['callback'])->shouldBeAnInstanceOf(CallableType::class);
    }

    function it_creates_a_resource_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['resource'])->shouldBeAnInstanceOf(ResourceType::class);
    }

    function it_creates_a_void_type_if_a_void_type_is_given()
    {
        $this->create(new Void(), [])->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_creates_a_void_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['void'])->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_creates_an_object_type_if_an_empty_object_type_is_given()
    {
        $this->create(new Object_(), [])->shouldBeAnInstanceOf(ObjectType::class);
    }
    
    function it_creates_an_object_type_if_no_type_is_given_but_a_phpdoc()
    {
        $this->create(null, ['object'])->shouldBeAnInstanceOf(ObjectType::class);
    }

    function it_creates_a_class_type_if_a_object_type_with_fqsen_is_given(Reflector $reflector)
    {
        $classReflection = ClassReflectionStubBuilder::build()->withClassName('stdClass')->finish();
        $reflector->reflectClass('stdClass')->willReturn($classReflection);
        $this->create(new Object_(new Fqsen('\\stdClass')), [])->shouldBeAReflectionOfClass('stdClass');
    }

    public function getMatchers()
    {
        return [
            'beAReflectionOfClass' => function (ClassType $type, string $expectedClass) {
                return $type->reflection()->name() === $expectedClass;
            }
        ];
    }
}
