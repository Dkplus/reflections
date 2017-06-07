<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\NullType;
use PhpSpec\ObjectBehavior;

class NullTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NullType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('null');
    }

    function it_accepts_null_values()
    {
        $this->accepts(new NullType())->shouldBe(true);
    }

    function it_does_not_accept_other_values(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
