<?php
namespace spec\Dkplus\Reflection;

use BetterReflection\Reflection\ReflectionParameter;
use Dkplus\Reflection\Parameter;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Parameter
 */
class ParameterSpec extends ObjectBehavior
{
    function let(ReflectionParameter $parameter, Type $type)
    {
        $this->beConstructedWith($parameter, $type, 0, true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parameter::class);
    }

    function it_has_a_name(ReflectionParameter $parameter)
    {
        $parameter->getName()->willReturn('id');
        $this->name()->shouldBe('id');
    }

    function it_has_a_position()
    {
        $this->position()->shouldBe(0);
    }

    function it_has_a_type(Type $type)
    {
        $this->type()->shouldBe($type);
    }

    function it_might_allow_types_to_be_passed(Type $type, Type $anotherType)
    {
        $type->accepts($anotherType)->willReturn(false);
        $this->allows($anotherType)->shouldBe(false);

        $type->accepts($anotherType)->willReturn(true);
        $this->allows($anotherType)->shouldBe(true);
    }

    function it_may_be_omitted()
    {
        $this->canBeOmitted()->shouldBe(true);
    }
}
