<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ScalarType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

class ScalarTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ScalarType::class);
    }

    function it_is_a_Type()
    {
        $this->shouldImplement(Type::class);
    }

    function its_string_representation_is_scalar()
    {
        $this->__toString()->shouldBe('scalar');
    }

    function it_accepts_a_StringType()
    {
        $this->accepts(new StringType())->shouldBe(true);
    }

    function it_accepts_an_IntegerType()
    {
        $this->accepts(new IntegerType())->shouldBe(true);
    }

    function it_accepts_a_FloatType()
    {
        $this->accepts(new FloatType())->shouldBe(true);
    }

    function it_accepts_a_BooleanType()
    {
        $this->accepts(new BooleanType())->shouldBe(true);
    }

    function it_does_not_accept_a_NullType()
    {
        $this->accepts(new NullType())->shouldBe(false);
    }

    function it_does_not_accept_other_Types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
