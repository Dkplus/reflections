<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\VoidType;
use PhpSpec\ObjectBehavior;

class MixedTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MixedType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('mixed');
    }

    function it_allows_all_types_except_void(Type $type)
    {
        $this->allows($type)->shouldBe(true);
        $this->allows(new VoidType())->shouldBe(false);
    }
}
