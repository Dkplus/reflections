<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\Exception\MissingParameter;
use Dkplus\Reflection\ParameterReflection;
use Dkplus\Reflection\Parameters;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;

/**
 * @method shouldIterateAs($data)
 */
class ParametersSpec extends ObjectBehavior
{
    function let(ParameterReflection $parameter)
    {
        $parameter->name()->willReturn('foo');
        $parameter->position()->willReturn(0);
        $this->beConstructedWith('MyClass::myMethod', $parameter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parameters::class);
    }

    function it_can_be_counted()
    {
        $this->shouldHaveCount(1);
    }

    function it_can_be_queried_to_know_whether_it_contains_parameters_or_not()
    {
        $this->contains('foo')->shouldBe(true);
        $this->contains('bar')->shouldBe(false);
    }

    function it_iterates_over_all_parameters(ParameterReflection $first, ParameterReflection $second)
    {
        $first->name()->willReturn('foo');
        $first->position()->willReturn(0);

        $second->name()->willReturn('bar');
        $second->position()->willReturn(1);

        $this->beConstructedWith('MyClass::myMethod', $first, $second);

        $this->shouldIterateAs([$first, $second]);
    }

    function it_provides_a_parameter_by_name(ParameterReflection $parameter)
    {
        $this->named('foo')->shouldBe($parameter);

        $this->shouldThrow(MissingParameter::named('bar', 'MyClass::myMethod'))->during('named', ['bar']);
    }

    function it_provides_a_parameter_by_its_position(ParameterReflection $parameter)
    {
        $this->atPosition(0)->shouldBe($parameter);

        $this->shouldThrow(MissingParameter::atPosition(1, 'MyClass::myMethod'))->during('atPosition', [1]);
    }

    function it_allows_types_if_all_types_are_allowed_by_the_parameters(
        ParameterReflection $firstParameter,
        ParameterReflection $secondParameter,
        Type $firstType,
        Type $secondType
    ) {
        $firstParameter->canBeOmitted()->willReturn(false);
        $firstParameter->allows($firstType)->willReturn(true);
        $firstParameter->name()->willReturn('foo');
        $secondParameter->canBeOmitted()->willReturn(false);
        $secondParameter->name()->willReturn('bar');
        $secondParameter->allows($secondType)->willReturn(true);

        $this->beConstructedWith('MyClass::myMethod', $firstParameter, $secondParameter);
        $this->allows($firstType, $secondType)->shouldBe(true);

        $secondParameter->allows($secondType)->willReturn(false);
        $this->allows($firstType, $secondType)->shouldBe(false);
    }

    function it_also_allows_types_if_parameters_of_missing_types_can_be_omitted(
        ParameterReflection $firstParameter,
        ParameterReflection $secondParameter,
        Type $firstType
    ) {
        $firstParameter->canBeOmitted()->willReturn(false);
        $firstParameter->allows($firstType)->willReturn(true);
        $firstParameter->name()->willReturn('foo');
        $secondParameter->canBeOmitted()->willReturn(true);
        $secondParameter->name()->willReturn('bar');

        $this->beConstructedWith('MyClass::MyMethod', $firstParameter, $secondParameter);
        $this->allows($firstType)->shouldBe(true);

        $secondParameter->canBeOmitted()->willReturn(false);
        $this->allows($firstType)->shouldBe(false);
    }

    function it_also_allows_types_if_more_types_are_given_as_parameters(Type $firstType)
    {
        $this->beConstructedWith('MyClass::MyMethod');
        $this->allows($firstType)->shouldBe(true);
    }
}
