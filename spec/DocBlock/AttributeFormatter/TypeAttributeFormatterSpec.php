<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\TypeAttributeFormatter;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;

class TypeAttributeFormatterSpec extends ObjectBehavior
{
    function let(AttributeFormatter $decorated)
    {
        $this->beConstructedWith($decorated, new TypeResolver());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeAttributeFormatter::class);
    }

    function it_is_an_AttributeFormatter()
    {
        $this->shouldImplement(AttributeFormatter::class);
    }

    function it_formats_the_attributes_by_using_a_decorated_AttributeFormatter(AttributeFormatter $decorated)
    {
        $context = new Context('My\\Namespace', []);

        $decorated->format(['Hinz'], $context)->willReturn(['name' => 'Hinz']);

        $this
            ->format(['Hinz'], $context)
            ->shouldBeLike(['name' => 'Hinz']);
    }

    function it_formats_an_attribute_with_key_type_to_a_phpdoc_Type(AttributeFormatter $decorated)
    {
        $context = new Context('My\\Namespace', []);

        $decorated->format(['string $name'], $context)->willReturn(['type' => 'string', 'name' => '$name']);

        $this
            ->format(['string $name'], $context)
            ->shouldBeLike(['type' => new String_(), 'name' => '$name']);
    }

    function it_formats_an_attribute_with_key_return_to_a_phpdoc_Type(AttributeFormatter $decorated)
    {
        $context = new Context('My\\Namespace', []);

        $decorated
            ->format(['string doSomething()'], $context)
            ->willReturn(['return' => 'string', 'method' => 'doSomething']);

        $this
            ->format(['string doSomething()'], $context)
            ->shouldBeLike(['return' => new String_(), 'method' => 'doSomething']);
    }

    function it_formats_each_type_element_inside_of_an_attribute_with_key_params_to_a_phpdoc_Type(
        AttributeFormatter $decorated
    ) {
        $context = new Context('My\\Namespace', []);

        $decorated
            ->format(['doSomething(string $foo, int $bar)'], $context)
            ->willReturn([
                'method' => 'doSomething',
                'params' => [
                    ['type' => 'string', 'name' => '$foo'],
                    ['type' => 'int', 'name' => '$bar']
                ]
            ]);

        $this
            ->format(['doSomething(string $foo, int $bar)'], $context)
            ->shouldBeLike([
                'method' => 'doSomething',
                'params' => [
                    ['type' => new String_(), 'name' => '$foo'],
                    ['type' => new Integer(), 'name' => '$bar']
                ]
            ]);
    }
}
