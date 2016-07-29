<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\ClassReflection;
use Dkplus\Reflections\Type\ClassType;
use Dkplus\Reflections\Type\CollectionType;
use Dkplus\Reflections\Type\DecoratingType;
use Dkplus\Reflections\Type\StringType;
use Dkplus\Reflections\Type\Type;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflections\Mock\ClassReflectionStubBuilder;
use Traversable;

/**
 * @mixin CollectionType
 */
class CollectionTypeSpec extends ObjectBehavior
{
    function let(ClassType $collection, ClassReflection $reflection, Type $generic)
    {
        $reflection->implementsInterface(Traversable::class)->willReturn(true);
        $collection->reflection()->willReturn($reflection);
        $this->beConstructedWith($collection, $generic);
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

    function its_class_must_be_traversable(ClassReflection $reflection)
    {
        $reflection->name()->willReturn('stdClass');
        $reflection->implementsInterface(Traversable::class)->willReturn(false);
        $this->shouldThrow(new InvalidArgumentException('Class stdClass is not traversable'))->duringInstantiation();
    }

    function its_string_representation_includes_the_collection_class_and_the_generic_type(
        ClassType $collection,
        Type $generic
    ) {
        $collection->__toString()->willReturn('Collection');
        $generic->__toString()->willReturn('string');

        $this->__toString()->shouldBe('Collection|string[]');
    }

    function it_allows_other_generic_collections_of_the_same_type(ClassType $collection, Type $generic)
    {
        $allowedCollectionClass = new ClassType(
            ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish()
        );
        $allowedGeneric = new StringType();

        $collection->allows($allowedCollectionClass)->willReturn(true);
        $generic->allows($allowedGeneric)->willReturn(true);

        $this->allows(new CollectionType($allowedCollectionClass, $allowedGeneric))->shouldBe(true);
    }

    function it_does_not_allow_collections_of_other_classes_with_the_same_generic(ClassType $collection, Type $generic)
    {
        $notAllowedCollectionClass = new ClassType(
            ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish()
        );
        $allowedGeneric = new StringType();

        $collection->allows($notAllowedCollectionClass)->willReturn(false);
        $generic->allows($allowedGeneric)->willReturn(true);

        $this->allows(new CollectionType($notAllowedCollectionClass, $allowedGeneric))->shouldBe(false);
    }

    function it_does_not_allow_collections_of_other_generics(ClassType $collection, Type $generic)
    {
        $allowedCollectionClass = new ClassType(
            ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish()
        );
        $notAllowedGeneric = new StringType();

        $collection->allows($allowedCollectionClass)->willReturn(true);
        $generic->allows($notAllowedGeneric)->willReturn(false);

        $this->allows(new CollectionType($allowedCollectionClass, $notAllowedGeneric))->shouldBe(false);
    }

    function it_does_not_allow_other_types(Type $anotherType)
    {
        $this->allows($anotherType)->shouldBe(false);
    }
}
