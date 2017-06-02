<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\ClassReflection_;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflection\Mock\ClassReflectionStubBuilder;
use Traversable;

class ClassTypeSpec extends ObjectBehavior
{
    function let(ClassReflection_ $reflection)
    {
        $reflection->name()->willReturn('MyClass');
        $this->beConstructedWith($reflection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_reflection(ClassReflection_ $reflection)
    {
        $this->reflection()->shouldBe($reflection);
    }

    function its_string_representation_is_its_class_name()
    {
        $this->__toString()->shouldBe('MyClass');
    }

    function it_allows_objects_of_the_same_class()
    {
        $this->allows(new ClassType(
            ClassReflectionStubBuilder::build()->withClassName('MyClass')->finish()
        ))->shouldBe(true);
    }

    function it_allows_objects_that_implement_it()
    {
        $anotherClass = ClassReflectionStubBuilder::build()->implement('MyClass')->finish();

        $this->allows(new ClassType($anotherClass))->shouldBe(true);
    }

    function it_allows_objects_that_extend_it()
    {
        $anotherClass = ClassReflectionStubBuilder::build()->extend('MyClass')->finish();

        $this->allows(new ClassType($anotherClass))->shouldBe(true);
    }

    function it_allows_collections_if_the_collection_class_is_of_this_class(ClassReflection_ $reflection)
    {
        $sameClassReflection = ClassReflectionStubBuilder::build()
            ->implement(Traversable::class)
            ->withClassName('MyClass')
            ->finish();
        $anotherClassReflection = ClassReflectionStubBuilder::build()
            ->implement(Traversable::class)
            ->finish();

        $this->allows(new CollectionType(new ClassType($sameClassReflection), new StringType()))->shouldBe(true);
        $this->allows(new CollectionType(new ClassType($anotherClassReflection), new StringType()))->shouldBe(false);
    }

    function it_does_not_allow_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
