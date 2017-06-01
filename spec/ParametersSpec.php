<?php
namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\MissingParameter;
use Dkplus\Reflection\Parameter;
use Dkplus\Reflection\Parameters;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Parameters
 */
class ParametersSpec extends ObjectBehavior
{
    function let(Parameter $parameter)
    {
        $parameter->name()->willReturn('foo');
        $parameter->position()->willReturn(0);
        $this->beConstructedWith('MyClass::myMethod', [$parameter]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parameters::class);
    }

    function it_has_a_size()
    {
        $this->size()->shouldBe(1);
    }

    function it_contains_parameters()
    {
        $this->contains('foo')->shouldBe(true);
        $this->contains('bar')->shouldBe(false);
    }

    function it_provides_all_parameters(Parameter $parameter)
    {
        $this->all()->shouldBeLike([$parameter]);
    }

    function it_provides_a_parameter_by_name(Parameter $parameter)
    {
        $this->named('foo')->shouldBe($parameter);

        $this->shouldThrow(MissingParameter::named('bar', 'MyClass::myMethod'))->during('named', ['bar']);
    }

    function it_provides_a_parameter_by_its_position(Parameter $parameter)
    {
        $this->atPosition(0)->shouldBe($parameter);

        $this->shouldThrow(MissingParameter::atPosition(1, 'MyClass::myMethod'))->during('atPosition', [1]);
    }

    function it_can_match_types(
        Parameter $firstParameter,
        Parameter $secondParameter,
        Parameter $thirdParameter,
        Type $firstType,
        Type $secondType
    ) {
        $this->beConstructedWith('MyClass::myMethod', [$firstParameter, $secondParameter, $thirdParameter]);

        $firstParameter->canBeOmitted()->willReturn(false);
        $firstParameter->name()->willReturn('foo');
        $secondParameter->canBeOmitted()->willReturn(false);
        $secondParameter->name()->willReturn('bar');
        $thirdParameter->canBeOmitted()->willReturn(true);
        $thirdParameter->name()->willReturn('baz');

        $firstParameter->allows($firstType)->willReturn(true);
        $secondParameter->allows($secondType)->willReturn(true);

        $this->allows($firstType, $secondType)->shouldBe(true);
        $this->allows($firstType)->shouldBe(false);

        $secondParameter->allows($secondType)->willReturn(false);
        $this->allows($firstType, $secondType)->shouldBe(false);
    }
}
