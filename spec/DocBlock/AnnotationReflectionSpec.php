<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use PhpSpec\ObjectBehavior;

class AnnotationReflectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('unqualified', ['deprecated', []]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AnnotationReflection::class);
    }

    function it_can_be_unqualified()
    {
        $this->beConstructedThrough('unqualified', ['deprecated', []]);
        $this->isFullyQualified()->shouldBe(false);
    }

    function it_can_be_fully_qualified()
    {
        $this->beConstructedThrough(
            'fullyQualified',
            ['Doctrine\\ORM\\Mapping\\Entity', []]
        );
        $this->isFullyQualified()->shouldBe(true);
    }

    public function it_has_a_tag()
    {
        $this->tag()->shouldBe('deprecated');
    }

    public function it_has_attributes()
    {
        $this->beConstructedThrough('unqualified', ['deprecated', ['since' => '1.0.0']]);
        $this->attributes()->shouldBeLike(['since' => '1.0.0']);
    }

    public function it_has_inherited_tags()
    {
        $this->beConstructedThrough('fullyQualified', [
            'Doctrine\\ORM\\Mapping\\Entity',
            [],
            AnnotationReflection::unqualified('Annotation', []),
            AnnotationReflection::unqualified('Target', [])
        ]);
        $this->attached()->containsAtLeastOneWithTag('Annotation')->shouldBe(true);
        $this->attached()->containsAtLeastOneWithTag('Target')->shouldBe(true);
        $this->attached()->containsAtLeastOneWithTag('deprecated')->shouldBe(false);
    }

    function it_inherited_tags_include_the_inherited_tags_of_the_inherited_tags()
    {
        $this->beConstructedThrough('fullyQualified', [
            'My\\Annotation',
            [],
            AnnotationReflection::fullyQualified(
                'Doctrine\\ORM\\Mapping\\Entity',
                [],
                AnnotationReflection::unqualified('Annotation', []),
                AnnotationReflection::unqualified('Target', [])
            )
        ]);
        $this->attached()->containsAtLeastOneWithTag('Doctrine\\ORM\\Mapping\\Entity')->shouldBe(true);
        $this->attached()->containsAtLeastOneWithTag('Annotation')->shouldBe(true);
    }

    function it_can_be_converted_to_string()
    {
        $this->__toString()->shouldBeString();
    }
}
