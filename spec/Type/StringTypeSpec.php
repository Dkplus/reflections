<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\StringType;
use PhpSpec\ObjectBehavior;

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

    function it_accepts_strings()
    {
        $this->accepts(new StringType())->shouldBe(true);
    }

    function it_accepts_no_other_types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
