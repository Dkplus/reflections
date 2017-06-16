<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\MultiTagAttributeFormatter;
use phpDocumentor\Reflection\Types\Context;
use PhpSpec\ObjectBehavior;

class MultiTagAttributeFormatterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MultiTagAttributeFormatter::class);
    }

    function it_formats_the_passed_attributes_with_an_AttributeFormatter_passed_by_constructor_for_a_specific_tag(
        AttributeFormatter $formatter
    ) {
        $context = new Context('MyNamespace');

        $formatter->format(['foo'], $context)->willReturn(['description' => 'foo']);

        $this->beConstructedWith(['deprecated' => $formatter]);
        $this->format('deprecated', ['foo'], $context)->shouldBeLike(['description' => 'foo']);
    }

    function it_returns_the_passed_attributes_if_no_AttributeFormatter_for_this_tag_exists()
    {
        $this->beConstructedWith([]);
        $this->format('deprecated', ['foo'], new Context('MyNamespace'))->shouldBe(['foo']);
    }

    function it_provides_a_default_instance_with_preregistered_AttributeFormatters_for_multiple_types()
    {
        $this->beConstructedThrough('forDefaultTags');
        $this->shouldBeAnInstanceOf(MultiTagAttributeFormatter::class);
    }
}
