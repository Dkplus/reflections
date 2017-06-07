<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use Traversable;

class CollectionTypeSpec extends ObjectBehavior
{
    function let(ReflectionClass $reflection, Type $generic)
    {
        $reflection->implementsInterface(Traversable::class)->willReturn(true);
        $this->beConstructedWith(new ClassType($reflection->getWrappedObject()), $generic);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CollectionType::class);
    }

    function it_is_a_class_type()
    {
        $this->shouldBeAnInstanceOf(ClassType::class);
    }

    function it_decorates_the_generic_type(Type $generic)
    {
        $this->decoratedType()->shouldBe($generic);
    }

    function its_class_must_be_traversable(ReflectionClass $reflection)
    {
        $reflection->getName()->willReturn('stdClass');
        $reflection->implementsInterface(Traversable::class)->willReturn(false);
        $reflection->isSubclassOf(Traversable::class)->willReturn(false);
        $this->shouldThrow(new InvalidArgumentException('Class \\stdClass is not traversable'))->duringInstantiation();
    }

    function its_string_representation_includes_the_collection_class_and_the_generic_type(
        ReflectionClass $reflection,
        Type $generic
    ) {
        $reflection->getName()->willReturn('Collection');
        $generic->__toString()->willReturn('string');

        $this->__toString()->shouldBe('\\Collection|string[]');
    }

    function it_allows_other_generic_collections_of_the_same_type(ReflectionClass $reflection, Type $generic)
    {
        $reflection->getName()->willReturn('MyClass');
        $allowedCollectionClass = new ClassType($reflection->getWrappedObject());
        $allowedGeneric = new StringType();

        $generic->accepts($allowedGeneric)->willReturn(true);

        $this->accepts(new CollectionType($allowedCollectionClass, $allowedGeneric))->shouldBe(true);
    }

    function it_allows_other_generic_collections_of_subtypes(
        ReflectionClass $reflection,
        ReflectionClass $otherClass,
        Type $generic
    ) {
        $reflection->getName()->willReturn('MyClass');
        $otherClass->getName()->willReturn('AnotherClass');
        $otherClass->implementsInterface(Traversable::class)->willReturn(true);
        $otherClass->implementsInterface('\\MyClass')->willReturn(false);
        $otherClass->isSubclassOf('\\MyClass')->willReturn(true);

        $allowedCollectionClass = new ClassType($otherClass->getWrappedObject());
        $allowedGeneric = new StringType();

        $generic->accepts($allowedGeneric)->willReturn(true);

        $this->accepts(new CollectionType($allowedCollectionClass, $allowedGeneric))->shouldBe(true);
    }

    function it_does_not_allow_collections_of_other_classes_with_the_same_generic(
        ReflectionClass $reflection,
        ReflectionClass $otherClass,
        Type $generic
    ) {
        $reflection->getName()->willReturn('MyClass');
        $otherClass->getName()->willReturn('AnotherClass');
        $otherClass->implementsInterface(Argument::any())->willReturn(false);
        $otherClass->isSubclassOf(Argument::any())->willReturn(false);
        $otherClass->implementsInterface(Traversable::class)->willReturn(true);
        $notAllowedCollectionClass = new ClassType($otherClass->getWrappedObject());
        $allowedGeneric = new StringType();

        $generic->accepts($allowedGeneric)->willReturn(true);

        $this->accepts(new CollectionType($notAllowedCollectionClass, $allowedGeneric))->shouldBe(false);
    }

    function it_does_not_allow_collections_of_other_generics(ReflectionClass $reflection, Type $generic)
    {
        $reflection->getName()->willReturn('MyClass');
        $allowedCollectionClass = new ClassType($reflection->getWrappedObject());
        $notAllowedGeneric = new StringType();

        $generic->accepts($notAllowedGeneric)->willReturn(false);

        $this->accepts(new CollectionType($allowedCollectionClass, $notAllowedGeneric))->shouldBe(false);
    }

    function it_does_not_allow_other_types(Type $anotherType)
    {
        $this->accepts($anotherType)->shouldBe(false);
    }
}
