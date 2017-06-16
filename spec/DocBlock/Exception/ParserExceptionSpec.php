<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\Exception;

use Dkplus\Reflection\DocBlock\Exception\ParserException;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class ParserExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ParserException::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_is_thrown_if_a_constant_cannot_be_resolved()
    {
        $this->beConstructedThrough('invalidConstant', ['foo']);
        $this->shouldHaveType(ParserException::class);
    }
}
