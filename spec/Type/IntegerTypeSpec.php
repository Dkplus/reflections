<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\IntegerType;
use PhpSpec\ObjectBehavior;

class IntegerTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IntegerType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('int');
    }

    function it_accepts_integers()
    {
        $this->accepts(new IntegerType())->shouldBe(true);
    }

    function it_does_not_accept_other_types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
