<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

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

    function it_accepts_resources()
    {
        $this->accepts(new ResourceType())->shouldBe(true);
    }

    function it_accepts_no_other_types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
