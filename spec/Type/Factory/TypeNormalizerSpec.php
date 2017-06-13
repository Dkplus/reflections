<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\Factory\TypeNormalizer;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\ScalarType;
use Dkplus\Reflection\Type\StringType;
use PhpSpec\ObjectBehavior;
use ReflectionClass;

class TypeNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypeNormalizer::class);
    }

    function it_normalizes_a_ComposedType_of_FloatType_IntegerType_BooleanType_and_StringType_into_a_ScalarType()
    {
        $this
            ->normalize(new ComposedType(new FloatType(), new IntegerType(), new BooleanType(), new StringType()))
            ->shouldBeLike(new ScalarType());
    }

    function it_normalizes_a_ComposedType_of_ScalarType_and_one_of_its_elements_into_a_ScalarType()
    {
        $this
            ->normalize(new ComposedType(new ScalarType(), new StringType()))
            ->shouldBeLike(new ScalarType());

        $this
            ->normalize(new ComposedType(new ScalarType(), new IntegerType(), new FloatType(), new BooleanType()))
            ->shouldBeLike(new ScalarType());

        $this
            ->normalize(new ComposedType(new ScalarType(), new IntegerType(), new CallableType()))
            ->shouldBeLike(new ComposedType(new ScalarType(), new CallableType()));
    }

    function it_normalizes_into_a_Scalar_also_if_more_Types_are_given()
    {
        $this->normalize(
            new ComposedType(new FloatType(), new IntegerType(), new BooleanType(), new StringType(), new CallableType())
        )->shouldBeLike(new ComposedType(new ScalarType(), new CallableType()));
    }

    function it_flattens_multiple_ComposedTypes_while_converting()
    {
        $this
            ->normalize(new ComposedType(new FloatType(), new ComposedType(new IntegerType(), new BooleanType())))
            ->shouldBeLike(new ComposedType(new FloatType(), new IntegerType(), new BooleanType()));
    }

    function it_removes_duplicates_from_ComposedTypes()
    {
        $this
            ->normalize(new ComposedType(new StringType(), new ComposedType(new StringType(), new NullType())))
            ->shouldBeLike(new NullableType(new StringType()));
    }

    function it_normalizes_a_Composed_with_one_Nullable_into_a_Nullable_ComposedType()
    {
        $this->normalize(new ComposedType(new IntegerType(), new NullableType(new FloatType())))
            ->shouldBeLike(new NullableType(new ComposedType(new IntegerType(), new FloatType())));
    }

    function it_normalizes_a_Composed_with_one_Null_into_a_nullable_ComposedType_or_a_NullableType_if_only_one_Type_was_decorated()
    {
        $this
            ->normalize(new ComposedType(new StringType(), new NullType()))
            ->shouldBeLike(new NullableType(new StringType()));
    }

    function it_normalizes_a_ComposedType_of_one_ArrayType_of_MixedType_and_an_ArrayType_of_anotherType_to_an_ArrayType_of_the_other_Type()
    {
        $this
            ->normalize(new ComposedType(new ArrayType(), new ArrayType(new StringType())))
            ->shouldBeLike(new ArrayType(new StringType()));
    }

    function it_normalizes_a_ComposedType_of_one_traversable_ClassType_with_an_IterableType_to_a_CollectionType()
    {
        $iterable = new ReflectionClass('\\ArrayObject');

        $this
            ->normalize(new ComposedType(new ClassType($iterable), new ArrayType(new StringType())))
            ->shouldBeLike(new CollectionType(new ClassType($iterable), new StringType()));
    }

    function it_normalizes_an_ArrayType_of_a_non_MixedType_into_a_IterableType_of_the_Type()
    {
        $this
            ->normalize(new ArrayType(new StringType()))
            ->shouldBeLike(new IterableType(new StringType()));
    }

    function it_allows_multiple_traversable_types_to_share_the_same_inner_Types()
    {
        $iterable = new ReflectionClass('\\ArrayObject');
        $innerType = new ComposedType(new FloatType(), new StringType());
        $this->normalize(new ComposedType(
            new ArrayType(),
            new IterableType(),
            new ClassType($iterable),
            new ArrayType(new FloatType()),
            new ArrayType(new StringType())
        ))->shouldBeLike(new ComposedType(
            new ArrayType($innerType),
            new IterableType($innerType),
            new CollectionType(new ClassType($iterable), $innerType)
        ));
    }
}
