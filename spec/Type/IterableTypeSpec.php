<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\DecoratingType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\StringType;
use PhpSpec\ObjectBehavior;
use ReflectionClass;
use Traversable;

class IterableTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IterableType::class);
    }

    function it_should_be_a_decorating_type()
    {
        $this->shouldImplement(DecoratingType::class);
    }

    function it_decorates_the_mixed_type_by_default()
    {
        $this->innerType()->shouldBeLike(new MixedType());
    }

    function its_string_representation_appends_array_brackets_to_the_decorated_type()
    {
        $this->__toString()->shouldBe('mixed[]');
    }

    function its_string_representation_puts_also_brackets_around_composed_decorated_types(ComposedType $type)
    {
        $type->__toString()->willReturn('string|int');
        $this->beConstructedWith($type);
        $this->__toString()->shouldBe('(string|int)[]');
    }

    function it_accepts_other_iterable_of_accepted_decorated_types()
    {
        $this->beConstructedWith(new StringType());

        $this->accepts(new StringType())->shouldBe(false);
        $this->accepts(new IterableType(new StringType()))->shouldBe(true);
        $this->accepts(new IterableType(new IntegerType()))->shouldBe(false);
    }

    function it_accepts_arrays_of_allowed_decorated_types()
    {
        $this->beConstructedWith(new StringType());

        $this->accepts(new ArrayType(new StringType()))->shouldBe(true);
        $this->accepts(new ArrayType(new IntegerType()))->shouldBe(false);
    }

    function it_accepts_traversable_objects_instances_if_its_decorated_type_is_mixed(
        ReflectionClass $traversable,
        ReflectionClass $nonTraversable
    ) {
        $traversable->implementsInterface(Traversable::class)->willReturn(true);

        $this->accepts(new ClassType($traversable->getWrappedObject()))->shouldBe(true);
        $this->accepts(new ClassType($nonTraversable->getWrappedObject()))->shouldBe(false);
    }

    function it_accepts_collections_instances_if_its_decorated_type_matches_the_generic_type(
        ReflectionClass $reflection
    ) {
        $this->beConstructedWith(new StringType());

        $reflection->implementsInterface(Traversable::class)->willReturn(true);

        $matchingType = new StringType();
        $notMatchingType = new MixedType();

        $this->accepts(new CollectionType(new ClassType($reflection->getWrappedObject()),
            $matchingType))->shouldBe(true);
        $this->accepts(new CollectionType(new ClassType($reflection->getWrappedObject()),
            $notMatchingType))->shouldBe(false);
    }

    function it_accepts_composed_types_if_all_parts_are_accepted()
    {
        $this->beConstructedWith(new BooleanType());

        $this
            ->accepts(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new BooleanType())))
            ->shouldBe(true);
        $this
            ->accepts(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new StringType())))
            ->shouldBe(false);
    }
}
