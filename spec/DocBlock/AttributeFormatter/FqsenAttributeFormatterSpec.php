<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\FqsenAttributeFormatter;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;
use PhpSpec\ObjectBehavior;

class FqsenAttributeFormatterSpec extends ObjectBehavior
{
    function let(AttributeFormatter $decorated)
    {
        $this->beConstructedWith($decorated, new FqsenResolver());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FqsenAttributeFormatter::class);
    }

    function it_is_an_AttributeFormatter()
    {
        $this->shouldImplement(AttributeFormatter::class);
    }

    function it_takes_the_attributes_from_a_decorated_AttributeFormatter_and_converts_a_fqsen_string_to_a_Fqsen(
        AttributeFormatter $decorated
    ) {
        $passedAttributes = ['RuntimeException', 'Throws a RuntimeException'];
        $attributesAfterDecorated = ['fqsen' => 'RuntimeException', 'description' => 'Throws a RuntimeException'];
        $context = new Context('MyNamespace', ['RuntimeException' => 'MyNamespace\\Exception\\RuntimeException']);

        $decorated->format($passedAttributes, $context)->willReturn($attributesAfterDecorated);

        $this
            ->format($passedAttributes, $context)
            ->shouldBeLike([
                'fqsen' => new Fqsen('\\MyNamespace\\Exception\\RuntimeException'),
                'description' => 'Throws a RuntimeException'
            ]);
    }

    function it_does_not_modify_the_attributes_from_the_decorated_AttributeFormatter_if_none_has_the_key_fqsen(
        AttributeFormatter $decorated
    ) {
        $passedAttributes = ['Hinz'];
        $attributesAfterDecorated = ['name' => 'Hinz'];
        $context = new Context('MyNamespace');

        $decorated->format($passedAttributes, $context)->willReturn($attributesAfterDecorated);

        $this
            ->format($passedAttributes, $context)
            ->shouldBeLike($attributesAfterDecorated);
    }
}
