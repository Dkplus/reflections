<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionParameter;
use BetterReflection\Reflection\ReflectionType;
use Dkplus\Reflections\Parameter;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Parameter
 */
class ParameterSpec extends ObjectBehavior
{
    function let(ReflectionParameter $parameter)
    {
        $this->beConstructedWith($parameter);
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

    function it_has_a_type(ReflectionParameter $parameter)
    {
        $parameter->getType()->willReturn(ReflectionType::createFromType(new String_(), true));
        $this->type()->shouldBe('string');
    }

    function its_type_is_mixed_if_no_type_hint_has_been_set(ReflectionParameter $parameter)
    {
        $parameter->getType()->willReturn(null);
        $this->type()->shouldBe('mixed');
    }

    function it_might_allow_null(ReflectionParameter $parameter)
    {
        $parameter->getType()->willReturn(ReflectionType::createFromType(new String_(), true));
        $this->allowsNull()->shouldBe(true);

        $parameter->getType()->willReturn(ReflectionType::createFromType(new String_(), false));
        $this->allowsNull()->shouldBe(false);
    }

    function it_allows_null_if_its_type_is_mixed(ReflectionParameter $parameter)
    {
        $parameter->getType()->willReturn(null);
        $this->allowsNull()->shouldBe(true);
    }

    function its_type_might_be_a_generic_array(ReflectionParameter $parameter)
    {
        $parameter->getType()->willReturn(ReflectionType::createFromType(new Array_(), false));
        $parameter->getDocBlockTypeStrings()->willReturn([]);

        $this->type()->shouldBe('array');
    }
}
