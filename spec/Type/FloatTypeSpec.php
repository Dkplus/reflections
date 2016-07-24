<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\FloatType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin FloatType
 */
class FloatTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FloatType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('float');
    }

    function it_allows_strings()
    {
        $this->allows(new FloatType())->shouldBe(true);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
