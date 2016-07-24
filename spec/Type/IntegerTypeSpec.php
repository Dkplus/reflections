<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\IntegerType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin IntegerType
 */
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

    function it_allows_integers()
    {
        $this->allows(new IntegerType())->shouldBe(true);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
