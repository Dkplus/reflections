<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\Exception\MissingProperty;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\PropertyReflection;
use PhpSpec\ObjectBehavior;

/**
 * @method shouldIterateAs($data)
 */
class PropertiesSpec extends ObjectBehavior
{
    function let(PropertyReflection $property)
    {
        $property->name()->willReturn('foo');
        $this->beConstructedWith('MyClass', $property);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Properties::class);
    }

    function it_can_be_counted()
    {
        $this->shouldHaveCount(1);
    }

    function it_can_iterate_over_the_properties(PropertyReflection $first, PropertyReflection $second)
    {
        $first->name()->willReturn('foo');
        $second->name()->willReturn('bar');
        $this->beConstructedWith('MyClass', $first, $second);
        $this->shouldIterateAs([$first, $second]);
    }

    function it_can_be_queried_whether_it_contains_properties()
    {
        $this->contains('foo')->shouldBe(true);
        $this->contains('bar')->shouldBe(false);
    }

    function it_provides_a_property_by_name(PropertyReflection $property)
    {
        $this->named('foo')->shouldBe($property);

        $this->shouldThrow(MissingProperty::inClass('bar', 'MyClass'))->during('named', ['bar']);
    }
}
