<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\DecoratingType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\StringType;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflection\Mock\ClassReflectionStubBuilder;
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
        $this->decoratedType()->shouldBeLike(new MixedType());
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

    function it_allows_other_iterable_of_allowed_decorated_types()
    {
        $this->beConstructedWith(new StringType());

        $this->allows(new StringType())->shouldBe(false);
        $this->allows(new IterableType(new StringType()))->shouldBe(true);
        $this->allows(new IterableType(new IntegerType()))->shouldBe(false);
    }

    function it_allows_arrays_of_allowed_decorated_types()
    {
        $this->beConstructedWith(new StringType());

        $this->allows(new ArrayType(new StringType()))->shouldBe(true);
        $this->allows(new ArrayType(new IntegerType()))->shouldBe(false);
    }

    function it_allows_traversable_objects_instances_if_its_decorated_type_is_mixed()
    {
        $traversable = ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish();
        $nonTraversable = ClassReflectionStubBuilder::build()->finish();

        $this->allows(new ClassType($traversable))->shouldBe(true);
        $this->allows(new ClassType($nonTraversable))->shouldBe(false);
    }

    function it_allows_collections_instances_if_its_decorated_type_matches_the_generic_type()
    {
        $this->beConstructedWith(new StringType());
        $matchingType = new StringType();
        $notMatchingType = new MixedType();
        $classReflection = ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish();

        $this->allows(new CollectionType(new ClassType($classReflection), $matchingType))->shouldBe(true);
        $this->allows(new CollectionType(new ClassType($classReflection), $notMatchingType))->shouldBe(false);
    }

    function it_allows_composed_types_if_all_parts_are_allowed()
    {
        $this->beConstructedWith(new BooleanType());

        $this
            ->allows(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new BooleanType())))
            ->shouldBe(true);
        $this
            ->allows(new ComposedType(new ArrayType(new BooleanType()), new ArrayType(new StringType())))
            ->shouldBe(false);
    }
}
