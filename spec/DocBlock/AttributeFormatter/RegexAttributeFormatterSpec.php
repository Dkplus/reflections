<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\RegexAttributeFormatter;
use phpDocumentor\Reflection\Types\Context;
use PhpSpec\ObjectBehavior;

class RegexAttributeFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RegexAttributeFormatter::class);
    }

    function it_is_an_AttributeFormatter()
    {
        $this->shouldImplement(AttributeFormatter::class);
    }

    function it_uses_the_first_matching_regExp_on_the_first_attribute_to_reformat_the_attributes()
    {
        $this->beConstructedWith(
            '/^(?P<description>(?!\d+\.\d+\.\d+).*)/',
            '/(?P<vector>\d+\.\d+.\d+)?\s*(?P<description>.*)/'
        );
        $context = new Context('MyNamespace', []);
        $this
            ->format(['Some description'], $context)
            ->shouldBeLike(['description' => 'Some description']);
        $this
            ->format(['1.0.3 Some description'], $context)
            ->shouldBeLike(['vector' => '1.0.3', 'description' => 'Some description']);
    }

    function it_returns_the_passed_attributes_if_no_regex_matches()
    {
        $this->format(['foo bar baz'], new Context('MyNamespace', []))->shouldBeLike(['foo bar baz']);
    }
}
