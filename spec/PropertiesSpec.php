<?php
namespace spec\Dkplus\Reflections;

use Dkplus\Reflections\MissingProperty;
use Dkplus\Reflections\Properties;
use Dkplus\Reflections\Property;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Properties
 */
class PropertiesSpec extends ObjectBehavior
{
    function let(Property $property)
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

    function it_provides_all_properties(Property $property)
    {
        $this->all()->shouldBeLike([$property]);
    }

    function it_provides_a_property_by_name(Property $property)
    {
        $this->named('foo')->shouldBe($property);

        $this->shouldThrow(MissingProperty::inClass('bar', 'MyClass'))->during('named', ['bar']);
    }
}
