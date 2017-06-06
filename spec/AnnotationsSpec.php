<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\Exception\MissingAnnotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;
use PhpSpec\ObjectBehavior;
use Traversable;

/**
 * @method shouldIterateAs($data)
 */
class AnnotationsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Annotations::class);
    }

    function it_iterates_over_all_annotations()
    {
        $annotations = [new Target(['value' => 'CLASS']), new Enum(['value' => ['BAR']])];
        $this->beConstructedWith($annotations);

        $this->shouldImplement(Traversable::class);
        $this->shouldIterateAs($annotations);
    }

    function it_can_be_counted()
    {
        $this->beConstructedWith([new Target(['value' => 'CLASS']), new Enum(['value' => ['BAR']])]);
        $this->count()->shouldBe(2);
    }

    function it_provides_a_single_annotation_of_a_specific_class()
    {
        $expectedAnnotation = new Target(['value' => 'CLASS']);
        $this->beConstructedWith([$expectedAnnotation]);

        $this->oneNamed(Target::class)->shouldBeLike($expectedAnnotation);
    }

    function it_throws_an_exception_if_it_could_not_provide_a_requested_annotation()
    {
        $this->shouldThrow(MissingAnnotation::class)->during('oneOfClass', [Target::class]);
    }

    function it_can_be_queried_to_know_whether_it_contains_an_annotation_of_a_specific_class()
    {
        $this->beConstructedWith([new Target(['value' => 'CLASS'])]);
        $this->contains(Target::class)->shouldBe(true);
        $this->contains(Enum::class)->shouldBe(false);
    }

    function it_provides_all_annotations_of_a_given_class()
    {
        $expectedAnnotations = [new Target(['value' => 'CLASS']), new Target(['value' => 'CLASS'])];
        $this->beConstructedWith(array_merge($expectedAnnotations, [new Enum(['value' => ['BAR']])]));

        $this->named(Target::class)->shouldBeAnInstanceOf(Annotations::class);
        $this->named(Target::class)->shouldIterateAs($expectedAnnotations);
    }
}
