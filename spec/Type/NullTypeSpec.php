<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\NullType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin NullType
 */
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

    function it_allows_null_values()
    {
        $this->allows(new NullType())->shouldBe(true);
    }

    function it_allows_no_other_values(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
