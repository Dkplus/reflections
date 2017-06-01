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

    function it_is_a_decorating_type()
    {
        $this->shouldImplement(DecoratingType::class);
    }

    function it_decorates_a_type(Type $decorated)
    {
        $this->decoratedType()->shouldBe($decorated);
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

    function it_allows_non_null_values_if_the_decorated_type_allows_it(Type $decorated)
    {
        $passedType = new StringType();
        $decorated->allows($passedType)->willReturn(false);
        $this->allows($passedType)->shouldBe(false);

        $decorated->allows($passedType)->willReturn(true);
        $this->allows($passedType)->shouldBe(true);
    }

    function it_allows_null_values()
    {
        $this->allows(new NullType());
    }

    function it_allows_nullable_types_if_the_decorated_type_is_allowed(Type $decorated)
    {
        $decorated->allows(Argument::type(StringType::class))->willReturn(true);
        $decorated->allows(Argument::type(VoidType::class))->willReturn(false);

        $this->allows(new NullableType(new StringType()))->shouldBe(true);
        $this->allows(new NullableType(new VoidType()))->shouldBe(false);
    }

    function it_allows_composed_types_if_all_types_are_allowed(Type $decorated)
    {
        $decorated->allows(Argument::type(StringType::class))->willReturn(false);

        $this->allows(new ComposedType(new NullType(), new NullType()))->shouldBe(true);

        $this->allows(new ComposedType(new NullType(), new StringType()))->shouldBe(false);
    }
}
