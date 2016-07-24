<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\ComposedType;
use Dkplus\Reflections\Type\FalseType;
use Dkplus\Reflections\Type\StringType;
use Dkplus\Reflections\Type\TrueType;
use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\BooleanType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin BooleanType
 */
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
