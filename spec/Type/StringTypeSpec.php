<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\StringType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin StringType
 */
class StringTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StringType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('string');
    }

    function it_allows_strings()
    {
        $this->allows(new StringType())->shouldBe(true);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
