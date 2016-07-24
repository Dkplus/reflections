<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\ResourceType;
use Dkplus\Reflections\Type\Type;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ResourceType
 */
class ResourceTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('resource');
    }

    function it_allows_resources()
    {
        $this->allows(new ResourceType())->shouldBe(true);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
