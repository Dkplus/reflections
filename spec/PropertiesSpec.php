<?php
namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\MissingProperty;
use Dkplus\Reflection\Properties;
use Dkplus\Reflection\PropertyReflection;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Properties
 */
class PropertiesSpec extends ObjectBehavior
{
    function let(PropertyReflection $property)
    {
        $property->name()->willReturn('foo');
        $this->beConstructedWith('MyClass', [$property]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Properties::class);
    }

    function it_has_a_size()
    {
        $this->size()->shouldBe(1);
    }

    function it_contains_properties()
    {
        $this->contains('foo')->shouldBe(true);
        $this->contains('bar')->shouldBe(false);
    }

    function it_provides_all_properties(PropertyReflection $property)
    {
        $this->all()->shouldBeLike([$property]);
    }

    function it_provides_a_property_by_name(PropertyReflection $property)
    {
        $this->named('foo')->shouldBe($property);

        $this->shouldThrow(MissingProperty::inClass('bar', 'MyClass'))->during('named', ['bar']);
    }
}
