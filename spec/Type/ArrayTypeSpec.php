<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\DecoratingType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\StringType;
use PhpSpec\ObjectBehavior;

class ArrayTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayType::class);
    }

    function it_is_a_decorating_type()
    {
        $this->shouldImplement(DecoratingType::class);
    }

    function it_decorates_the_mixed_type_by_default()
    {
        $this->decoratedType()->shouldBeAnInstanceOf(MixedType::class);
    }

    function its_string_representation_contains_the_decorated_type()
    {
        $this->beConstructedWith(new StringType());
        $this->__toString()->shouldBe('array<string>');
    }

    function its_string_representation_does_not_contain_mixed_if_this_is_decorated()
    {
        $this->__toString()->shouldBe('array');
    }

    function it_accepts_other_arrays_of_accepted_decorated_types()
    {
        $this->beConstructedWith(new StringType());

        $this->accepts(new StringType())->shouldBe(false);
        $this->accepts(new ArrayType(new StringType()))->shouldBe(true);
        $this->accepts(new ArrayType(new IntegerType()))->shouldBe(false);
    }

    function it_accepts_composed_types_if_all_parts_are_accepted()
    {
        $this->beConstructedWith(new BooleanType());

        $this
            ->accepts(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new BooleanType())))
            ->shouldBe(true);
        $this
            ->accepts(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new StringType())))
            ->shouldBe(false);
    }
}
