<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\DocBlock\DocBlockReflection;
use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;
use PhpSpec\ObjectBehavior;

class DocBlockReflectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('', '');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DocBlockReflection::class);
    }

    function it_has_a_summary()
    {
        $this->beConstructedWith('My short summary', '');
        $this->summary()->shouldBe('My short summary');
    }

    function it_has_a_description()
    {
        $this->beConstructedWith('', 'My long description');
        $this->description()->shouldBe('My long description');
    }

    function it_has_annotations()
    {
        $this->annotations()->shouldBeAnInstanceOf(Annotations::class);
        $this->annotations()->shouldIterateAs([]);
    }

    function its_annotations_might_include_annotations_of_the_annotated_annotations_aka_attached_annotations()
    {
        $inheritedAnnotation = AnnotationReflection::unqualified('Annotation', []);
        $annotation = AnnotationReflection::fullyQualified('MyAnnotation', [], $inheritedAnnotation);
        $this->beConstructedWith('', '', $annotation);

        $this->annotations(true)->shouldIterateAs([$annotation, $inheritedAnnotation]);
    }

    function it_knows_the_number_of_annotations_within_itself()
    {
        $this->beConstructedWith('', '', AnnotationReflection::unqualified('Annotation', []));
        $this->countAnnotations()->shouldBe(1);
    }

    function it_knows_the_number_of_annotations_within_itself_including_the_attached_annotations()
    {
        $attachedAnnotation = AnnotationReflection::unqualified('Annotation', []);
        $annotation = AnnotationReflection::fullyQualified('MyAnnotation', [], $attachedAnnotation);
        $this->beConstructedWith('', '', $annotation);

        $this->countAnnotations(true)->shouldBe(2);
    }

    function it_knows_whether_a_specific_tag_has_been_set()
    {
        $this->beConstructedWith('', '', AnnotationReflection::unqualified('ignore', []));
        $this->hasTag('ignore')->shouldBe(true);
        $this->hasTag('deprecated')->shouldBe(false);
    }

    function it_also_knows_about_tags_written_in_the_doc_blocks_of_the_annotations()
    {
        $attachedAnnotation = AnnotationReflection::unqualified('Annotation', []);
        $annotation = AnnotationReflection::fullyQualified('MyAnnotation', [], $attachedAnnotation);
        $this->beConstructedWith('', '', $annotation);

        $this->hasTag('MyAnnotation', true)->shouldBe(true);
        $this->hasTag('Annotation', true)->shouldBe(true);
        $this->hasTag('deprecated', true)->shouldBe(false);
    }

    function it_provides_the_first_annotation_of_a_specific_tag_or_throws_a_MissingAnnotation_exception_if_such_tag_is_not_available()
    {
        $this->beConstructedWith(
            '',
            '',
            AnnotationReflection::unqualified('todo', []),
            AnnotationReflection::unqualified('todo', ['description' => 'refactor'])
        );
        $this
            ->oneAnnotationWithTag('todo')
            ->shouldBeLike(AnnotationReflection::unqualified('todo', []));
        $this
            ->shouldThrow(MissingAnnotation::class)
            ->during('oneAnnotationWithTag', ['ignore']);
    }

    function it_can_look_also_into_the_inherited_tags_when_looking_for_one_annotation_of_a_specific_tag()
    {
        $inheritedAnnotation = AnnotationReflection::unqualified('Annotation', []);
        $annotation = AnnotationReflection::fullyQualified('MyAnnotation', [], $inheritedAnnotation);
        $this->beConstructedWith('', '', $annotation);

        $this->oneAnnotationWithTag('Annotation', true)->shouldBe($inheritedAnnotation);
        $this->shouldThrow(MissingAnnotation::class)->during('oneAnnotationWithTag', ['Annotation']);
    }

    function it_provides_all_annotations_of_a_specific_tag()
    {
        $this->beConstructedWith(
            '',
            '',
            AnnotationReflection::unqualified('deprecated', []),
            AnnotationReflection::unqualified('todo', []),
            AnnotationReflection::unqualified('todo', ['description' => 'refactor'])
        );

        $this->annotationsWithTag('todo')->shouldBeAnInstanceOf(Annotations::class);
        $this->annotationsWithTag('todo')->shouldHaveCount(2);
    }

    function it_might_include_the_inherited_annotations_when_providing_all_annotations_of_a_specific_tag()
    {
        $inheritedAnnotation = AnnotationReflection::unqualified('Annotation', []);
        $annotation = AnnotationReflection::fullyQualified('MyAnnotation', [], $inheritedAnnotation);
        $this->beConstructedWith('', '', $annotation);

        $this->annotationsWithTag('Annotation')->shouldHaveCount(0);
        $this->annotationsWithTag('Annotation', true)->shouldHaveCount(1);
    }
}
