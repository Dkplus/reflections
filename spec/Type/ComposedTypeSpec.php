<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\ComposedType;
use Dkplus\Reflections\Type\IntegerType;
use Dkplus\Reflections\Type\StringType;
use Dkplus\Reflections\Type\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ComposedType
 */
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

    function it_allows_a_type_if_one_of_the_decorated_types_allows_it(Type $firstType, Type $secondType)
    {
        $this->beConstructedWith($firstType, $secondType);
        $firstType->allows(Argument::type(StringType::class))->willReturn(false);
        $secondType->allows(Argument::type(StringType::class))->willReturn(true);

        $this->allows(new StringType())->shouldBe(true);

        $secondType->allows(Argument::type(StringType::class))->willReturn(false);

        $this->allows(new StringType())->shouldBe(false);
    }
}