<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

class BooleanTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BooleanType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('bool');
    }

    function it_allows_other_booleans()
    {
        $this->allows(new BooleanType())->shouldBe(true);
    }

    function it_allows_composed_types_if_all_parts_are_allowed()
    {
        $this->allows(new ComposedType(new BooleanType(), new BooleanType()))->shouldBe(true);
        $this->allows(new ComposedType(new BooleanType(), new StringType()))->shouldBe(false);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
