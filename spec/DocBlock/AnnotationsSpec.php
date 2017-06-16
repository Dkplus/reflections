<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock;

use Countable;
use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\DocBlock\Exception\MissingAnnotation;
use PhpSpec\ObjectBehavior;
use Traversable;

/**
 * @method shouldIterateAs($data)
 */
class AnnotationsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Annotations::class);
    }

    function it_contain_AnnotationReflections()
    {
        $this->beConstructedWith(AnnotationReflection::unqualified('author', ['name' => 'Hinz']));
        $this->containsAtLeastOneWithTag('author')->shouldBe(true);
        $this->containsAtLeastOneWithTag('copyright')->shouldBe(false);
    }

    function it_iterate_over_AnnotationReflections()
    {
        $annotations = [
            AnnotationReflection::unqualified('author', ['name' => 'Hinz']),
            AnnotationReflection::unqualified('author', ['name' => 'Kunz'])
        ];
        $this->beConstructedWith(...$annotations);

        $this->shouldImplement(Traversable::class);
        $this->shouldIterateAs($annotations);
    }

    function it_are_counted()
    {
        $this->beConstructedWith(
            AnnotationReflection::unqualified('author', ['name' => 'Hinz']),
            AnnotationReflection::unqualified('author', ['name' => 'Kunz'])
        );
        $this->shouldImplement(Countable::class);

        $this
            ->count()
            ->shouldBe(2);
    }

    function it_provide_access_to_a_single_AnnotationReflection_of_a_given_tag()
    {
        $annotation = AnnotationReflection::unqualified('author', ['name' => 'Hinz']);
        $this->beConstructedWith($annotation);

        $this
            ->oneWithTag('author')
            ->shouldBeLike($annotation);
    }

    function it_throw_a_MissingAnnotationException_if_they_contain_no_AnnotationReflection_of_a_given_tag_but_one_was_assumed()
    {
        $this
            ->shouldThrow(MissingAnnotation::class)
            ->during('oneWithTag', ['author']);
    }

    function it_filter_AnnotationReflections_by_their_tag()
    {
        $authorAnnotations = [
            AnnotationReflection::unqualified('author', ['name' => 'Hinz']),
            AnnotationReflection::unqualified('author', ['name' => 'Kunz'])
        ];
        $otherAnnotations = [AnnotationReflection::unqualified('deprecated', [])];
        $this->beConstructedWith(...$authorAnnotations, ...$otherAnnotations);

        $this->withTag('author')->shouldBeAnInstanceOf(Annotations::class);
        $this->withTag('author')->shouldIterateAs($authorAnnotations);
    }

    function it_can_map_the_AnnotationReflections_using_a_callback()
    {
        $this->beConstructedWith(
            AnnotationReflection::unqualified('author', ['name' => 'Hinz']),
            AnnotationReflection::unqualified('author', ['name' => 'Kunz'])
        );
        $this->map(function (AnnotationReflection $reflection) {
            return $reflection->attributes()['name'];
        })->shouldBeLike(['Hinz', 'Kunz']);
    }

    function it_can_merge_multiple_instances()
    {
        $hinz = AnnotationReflection::unqualified('author', ['name' => 'Hinz']);
        $kunz = AnnotationReflection::unqualified('author', ['name' => 'Kunz']);

        $this->beConstructedWith($hinz);
        $this->merge()->shouldBeLike($this->getWrappedObject());
        $this->merge(new Annotations($kunz))->shouldBeLike(new Annotations($hinz, $kunz));
        $this->merge(new Annotations($kunz), new Annotations($hinz))->shouldBeLike(new Annotations($hinz, $kunz));
    }

    function it_can_include_the_inherited_AnnotationReflections()
    {
        $this->beConstructedWith(AnnotationReflection::fullyQualified(
            'My\\Annotation',
            [],
            AnnotationReflection::unqualified('Annotation', []),
            AnnotationReflection::fullyQualified(
                'Doctrine\\ORM\\Mapping\\Entity',
                [],
                AnnotationReflection::unqualified('Annotation', []),
                AnnotationReflection::unqualified('Target', [])
            )
        ));
        $this->shouldHaveCount(1);
        //one @Annotation is removed because its available twice
        $this->includeInherited()->shouldHaveCount(4);
    }
}
