<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\Exception;

use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class MissingAnnotationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MissingAnnotation::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldBeAnInstanceOf(RuntimeException::class);
    }

    function it_is_thrown_if_an_Annotation_of_a_specific_tag_could_not_be_found_within_a_docBlock()
    {
        $this->beConstructedThrough('withTag', ['var']);
        $this->getMessage()->shouldContain('@var');
    }
}
