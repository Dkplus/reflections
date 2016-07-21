<?php
namespace spec\Dkplus\Reflections;

use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\MissingAnnotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;
use PhpSpec\ObjectBehavior;
use Traversable;

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

    function it_is_iterable()
    {
        $this->shouldImplement(Traversable::class);
    }

    function it_provides_all_annotations_of_a_class()
    {
        $expectedAnnotations = [new Target(['value' => "CLASS"])];
        $this->beConstructedWith($expectedAnnotations);

        $this->all()->shouldBeLike($expectedAnnotations);
    }

    function it_provides_a_single_annotation()
    {
        $expectedAnnotation = new Target(['value' => "CLASS"]);
        $this->beConstructedWith([$expectedAnnotation]);

        $this->oneOfClass(Target::class)->shouldBeLike($expectedAnnotation);
    }

    function it_throws_an_exception_if_it_could_not_provide_a_requested_annotation()
    {
        $this->shouldThrow(MissingAnnotation::class)->during('oneOfClass', [Target::class]);
    }

    function it_knows_whether_it_could_provide_an_annotation()
    {
        $this->beConstructedWith([new Target(['value' => "CLASS"])]);
        $this->contains(Target::class)->shouldBe(true);
        $this->contains(Enum::class)->shouldBe(false);
    }

    function it_provides_all_annotations_of_a_given_class()
    {
        $expectedAnnotations = [new Target(['value' => "CLASS"]), new Target(['value' => "CLASS"])];
        $this->beConstructedWith(array_merge($expectedAnnotations, [new Enum(["value" => ['BAR']])]));

        $this->ofClass(Target::class)->shouldBeLike($expectedAnnotations);
    }
}
