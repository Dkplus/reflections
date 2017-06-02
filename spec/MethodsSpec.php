<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\Exception\MissingMethod;
use Dkplus\Reflection\MethodReflection;
use Dkplus\Reflection\Methods;
use PhpSpec\ObjectBehavior;

/**
 * @method shouldIterateAs($data)
 */
class MethodsSpec extends ObjectBehavior
{
    function let(MethodReflection $method)
    {
        $method->name()->willReturn('getFoo');
        $this->beConstructedWith('MyClass', $method);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Methods::class);
    }

    function it_can_be_counted()
    {
        $this->shouldHaveCount(1);
    }

    function it_can_be_queried_whether_it_contains_methods_with_specific_names()
    {
        $this->contains('getFoo')->shouldBe(true);
        $this->contains('getBar')->shouldBe(false);
    }

    function it_can_iterate_over_the_methods(MethodReflection $first, MethodReflection $second)
    {
        $first->name()->willReturn('getFoo');
        $second->name()->willReturn('getBar');
        $this->beConstructedWith('MyClass', $first, $second);
        $this->shouldIterateAs([$first, $second]);
    }

    function it_provides_to_a_method_by_name(MethodReflection $method)
    {
        $this->named('getFoo')->shouldBe($method);

        $this->shouldThrow(MissingMethod::inClass('getBar', 'MyClass'))->during('named', ['getBar']);
    }
}
