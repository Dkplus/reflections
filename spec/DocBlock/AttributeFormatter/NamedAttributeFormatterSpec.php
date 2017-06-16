<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\NamedAttributeFormatter;
use phpDocumentor\Reflection\Types\Context;
use PhpSpec\ObjectBehavior;

class NamedAttributeFormatterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('description');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NamedAttributeFormatter::class);
    }

    function it_is_an_AttributeFormatter()
    {
        $this->shouldImplement(AttributeFormatter::class);
    }

    function it_takes_the_first_attribute_as_value_for_the_name_given_in_the_constructor()
    {
        $this->format(['foo'], new Context('MyNamespace'))->shouldBeLike(['description' => 'foo']);
    }

    function it_uses_an_empty_string_as_value_if_no_value_has_been_passed()
    {
        $this->format([], new Context('MyNamespace'))->shouldBeLike(['description' => '']);
    }
}
