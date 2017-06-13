<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\DecoratingType;
use Dkplus\Reflection\Type\VoidType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NullableTypeSpec extends ObjectBehavior
{
    function let(Type $decorated)
    {
        $this->beConstructedWith($decorated);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NullableType::class);
    }

    function it_decorates_a_type(Type $decorated)
    {
        $this->shouldImplement(DecoratingType::class);
        $this->innerType()->shouldBe($decorated);
    }

    function it_prepends_the_string_representation_of_its_decorated_type_with_a_question_mark(Type $decorated)
    {
        $decorated->__toString()->willReturn('string');
        $this->__toString()->shouldBe('?string');
    }

    function its_string_representation_surrounds_the_decorated_type_with_brackets_if_its_a_composed_type(
        ComposedType $type
    ) {
        $type->__toString()->willReturn('string|int');
        $this->beConstructedWith($type);

        $this->__toString()->shouldBe('?(string|int)');
    }

    function it_accepts_non_null_values_if_the_decorated_type_accepts_it(Type $decorated)
    {
        $passedType = new StringType();
        $decorated->accepts($passedType)->willReturn(false);
        $this->accepts($passedType)->shouldBe(false);

        $decorated->accepts($passedType)->willReturn(true);
        $this->accepts($passedType)->shouldBe(true);
    }

    function it_accepts_null_values()
    {
        $this->accepts(new NullType());
    }

    function it_accepts_nullable_types_if_the_decorated_type_is_allowed(Type $decorated)
    {
        $decorated->accepts(Argument::type(StringType::class))->willReturn(true);
        $decorated->accepts(Argument::type(VoidType::class))->willReturn(false);

        $this->accepts(new NullableType(new StringType()))->shouldBe(true);
        $this->accepts(new NullableType(new VoidType()))->shouldBe(false);
    }

    function it_accepts_composed_types_if_all_types_are_allowed(Type $decorated)
    {
        $decorated->accepts(Argument::type(StringType::class))->willReturn(false);

        $this->accepts(new ComposedType(new NullType(), new NullType()))->shouldBe(true);

        $this->accepts(new ComposedType(new NullType(), new StringType()))->shouldBe(false);
    }
}
