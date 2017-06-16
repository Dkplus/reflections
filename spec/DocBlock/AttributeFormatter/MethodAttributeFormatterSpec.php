<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\MethodAttributeFormatter;
use phpDocumentor\Reflection\Types\Context;
use PhpSpec\ObjectBehavior;

class MethodAttributeFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MethodAttributeFormatter::class);
    }

    function it_is_an_AttributeFormatter()
    {
        $this->shouldImplement(AttributeFormatter::class);
    }

    function it_founds_the_return_type_and_the_name_of_a_method_tag()
    {
        $this
            ->format(['string myMethod()'], new Context('MyNamespace', []))
            ->shouldBeLike(['return' => 'string', 'name' => 'myMethod', 'params' => []]);
    }

    function it_assumes_void_as_return_type_if_no_return_type_is_given()
    {
        $this
            ->format(['myMethod()'], new Context('MyNamespace', []))
            ->shouldBeLike(['return' => 'void', 'name' => 'myMethod', 'params' => []]);
    }

    function it_founds_the_parameters_of_a_method_tag()
    {
        $this
            ->format(['string myMethod(string $param1, int $param2)'], new Context('MyNamespace', []))
            ->shouldBeLike(['return' => 'string', 'name' => 'myMethod', 'params' => [
                ['type' => 'string', 'name' => '$param1'],
                ['type' => 'int', 'name' => '$param2']
            ]]);
    }

    function it_assumes_mixed_as_type_for_each_parameter_that_has_no_type()
    {
        $this
            ->format(['string myMethod($param)'], new Context('MyNamespace', []))
            ->shouldBeLike(['return' => 'string', 'name' => 'myMethod', 'params' => [
                ['type' => 'mixed', 'name' => '$param'],
            ]]);
    }

    function it_returns_the_passed_attributes_of_the_attributes_dont_match_the_method_format()
    {
        $context = new Context('MyNamespace', []);
        $this->format([], $context)->shouldBeLike([]);
        $this->format(['myMethod(int)'], $context)->shouldBeLike(['myMethod(int)']);
    }
}
