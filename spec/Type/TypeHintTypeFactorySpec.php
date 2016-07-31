<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Reflector;
use Dkplus\Reflections\Type\BooleanType;
use Dkplus\Reflections\Type\CallableType;
use Dkplus\Reflections\Type\ClassType;
use Dkplus\Reflections\Type\FloatType;
use Dkplus\Reflections\Type\IntegerType;
use Dkplus\Reflections\Type\ObjectType;
use Dkplus\Reflections\Type\StringType;
use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\TypeFactory;
use Dkplus\Reflections\Type\TypeHintTypeFactory;
use Dkplus\Reflections\Type\VoidType;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin TypeHintTypeFactory
 */
class TypeHintTypeFactorySpec extends ObjectBehavior
{
    function let(TypeFactory $decorated)
    {
        $this->beConstructedWith($decorated);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeHintTypeFactory::class);
    }

    function it_is_a_type_factory()
    {
        $this->shouldImplement(TypeFactory::class);
    }

    function it_creates_a_string_type_if_a_string_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new String_(), [], false)->shouldBeAnInstanceOf(StringType::class);
    }

    function it_creates_an_integer_type_if_an_integer_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Integer(), [], false)->shouldBeAnInstanceOf(IntegerType::class);
    }

    function it_creates_a_float_type_if_a_float_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Float_(), [], false)->shouldBeAnInstanceOf(FloatType::class);
    }

    function it_creates_a_bool_type_if_a_bool_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Boolean(), [], false)->shouldBeAnInstanceOf(BooleanType::class);
    }

    function it_creates_a_callable_if_a_callable_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Callable_(), [], false)->shouldBeAnInstanceOf(CallableType::class);
    }

    function it_creates_a_void_type_if_a_void_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Void(), [], false)->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_creates_an_object_type_if_an_empty_object_type_is_given(Reflector $reflector)
    {
        $this->create($reflector, new Object_(), [], false)->shouldBeAnInstanceOf(ObjectType::class);
    }

    function it_let_the_decorated_factory_create_the_type_if_a_object_type_with_fqsen_is_given(
        Reflector $reflector,
        TypeFactory $decorated,
        Type $type
    ) {
        $givenType = new Object_(new Fqsen('\\Collection'));
        $decorated->create($reflector, $givenType, ['string[]', 'Collection'], false)->willReturn($type);

        $this->create($reflector, $givenType, ['string[]'], false)->shouldBe($type);
        $this->create($reflector, $givenType, ['string[]', 'Collection'], false)->shouldBe($type);
    }

    function it_let_the_decorated_factory_create_the_type_if_a_array_type_is_given(
        Reflector $reflector,
        TypeFactory $decorated,
        Type $type
    ) {
        $decorated->create($reflector, Argument::any(), ['string[]', 'array'], false)->willReturn($type);

        $this->create($reflector, new Array_(), ['string[]'], false)->shouldBe($type);
        $this->create($reflector, new Array_(), ['string[]', 'array'], false)->shouldBe($type);
    }

    function it_let_the_decorated_factory_create_the_type_if_a_mixed_type_is_given(
        Reflector $reflector,
        TypeFactory $decorated,
        Type $type
    ) {
        $decorated->create($reflector, Argument::any(), Argument::any(), Argument::any())->willReturn($type);
        $this->create($reflector, new Mixed(), [], false)->shouldBe($type);
    }

    public function getMatchers()
    {
        return [
            'beAReflectionOfClass' => function (Type $subject, string $expectedClass) {
                return $subject instanceof ClassType && $subject->reflection()->name() === $expectedClass;
            },
        ];
    }
}
