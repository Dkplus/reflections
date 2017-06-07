<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ComposedTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new StringType(), new IntegerType());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ComposedType::class);
    }

    function it_is_a_decorating_multiple_type()
    {
        $this->decoratedTypes()->shouldBeLike([new StringType(), new IntegerType()]);
    }

    function its_string_representation_separates_each_type_with_a_pipe()
    {
        $this->beConstructedWith(new StringType(), new IntegerType());
        $this->__toString()->shouldBe('string|int');
    }

    function it_accepts_a_type_if_one_of_the_decorated_types_allows_it(Type $firstType, Type $secondType)
    {
        $this->beConstructedWith($firstType, $secondType);
        $firstType->accepts(Argument::type(StringType::class))->willReturn(false);
        $secondType->accepts(Argument::type(StringType::class))->willReturn(true);

        $this->accepts(new StringType())->shouldBe(true);

        $secondType->accepts(Argument::type(StringType::class))->willReturn(false);

        $this->accepts(new StringType())->shouldBe(false);
    }
}
