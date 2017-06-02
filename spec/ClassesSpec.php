<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use BadMethodCallException;
use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Exception\EmptyClasses;
use PhpSpec\ObjectBehavior;

/**
 * @method shouldIterateAs($data)
 */
class ClassesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Classes::class);
    }

    function it_iterates_over_the_classes(ClassReflection $first, ClassReflection $second)
    {
        $this->beConstructedWith($first, $second);
        $this->shouldIterateAs([$first, $second]);
    }

    function it_can_be_counted(ClassReflection $first, ClassReflection $second)
    {
        $this->beConstructedWith($first, $second);
        $this->shouldHaveCount(2);
    }

    function it_can_filter_the_classes(ClassReflection $first, ClassReflection $second)
    {
        $this->beConstructedWith($first, $second);
        $this->filter(function (ClassReflection $reflection) use ($first) {
            return $first->getWrappedObject() === $reflection;
        })->shouldHaveCount(1);
    }

    function it_can_map_the_classes(ClassReflection $first, ClassReflection $second)
    {
        $first->name()->willReturn('Foo');
        $second->name()->willReturn('Bar');
        $this->beConstructedWith($first, $second);

        $this->map(function (ClassReflection $reflection) {
            return $reflection->name();
        })->shouldBe(['Foo', 'Bar']);
    }

    function it_can_merge_multiple_collections(ClassReflection $first, ClassReflection $second, ClassReflection $third)
    {
        $this->beConstructedWith($first);
        $this
            ->merge(new Classes($second->getWrappedObject()), new Classes($third->getWrappedObject()))
            ->shouldHaveCount(3);
    }

    function it_provides_the_first_class(ClassReflection $first)
    {
        $this->beConstructedWith($first);
        $this->first()->shouldBe($first);
    }

    function it_throws_an_exception_if_it_is_empty_and_the_first_class_is_retrieved()
    {
        $this->beConstructedWith();
        $this->shouldThrow(EmptyClasses::class)->during('first');
    }
}
