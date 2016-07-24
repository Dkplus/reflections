<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\VoidType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin VoidType
 */
class VoidTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(VoidType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('void');
    }

    function it_does_not_allow_any_value(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
