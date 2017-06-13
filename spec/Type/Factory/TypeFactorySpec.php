<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\Factory\TypeConverter;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use Dkplus\Reflection\Type\Factory\TypeNormalizer;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\StringType;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeFactorySpec extends ObjectBehavior
{
    function let(TypeConverter $converter, TypeNormalizer $normalizer)
    {
        $this->beConstructedWith($converter, $normalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeFactory::class);
    }

    function it_creates_a_MixedType_if_both_types_are_Mixed(TypeConverter $converter, TypeNormalizer $normalizer)
    {
        $context = new Fqsen('\\MyClass');

        $converter->convert(new Mixed(), $context)->willReturn(new MixedType());
        $normalizer->normalize(Argument::any())->willReturnArgument();

        $this
            ->create(new Mixed(), new Mixed(), $context)
            ->shouldBeLike(new MixedType());
    }

    function it_returns_the_one_type_that_is_not_Mixed_if_the_other_one_is(
        TypeConverter $converter,
        TypeNormalizer $normalizer
    ) {
        $context = new Fqsen('\\MyClass');

        $converter->convert(new Mixed(), $context)->willReturn(new MixedType());
        $converter->convert(new String_(), $context)->willReturn(new StringType());
        $normalizer->normalize(Argument::any())->willReturnArgument();

        $this->create(new Mixed(), new String_(), $context)->shouldBeLike(new StringType());
        $this->create(new String_(), new Mixed(), $context)->shouldBeLike(new StringType());
    }

    function it_provides_normalized_types(TypeConverter $converter, TypeNormalizer $normalizer)
    {
        $context = new Fqsen('\\MyClass');

        $compoundStrings = new Compound([new String_(), new String_()]);
        $composedStrings = new ComposedType(new StringType(), new StringType());

        $converter->convert(new Mixed(), $context)->willReturn(new MixedType());
        $converter->convert($compoundStrings, $context)->willReturn($composedStrings);
        $normalizer->normalize(Argument::any())->willReturnArgument();
        $normalizer->normalize($composedStrings)->willReturn(new StringType());

        $this->create(new Mixed(), $compoundStrings, $context)->shouldBeLike(new StringType());
        $this->create($compoundStrings, new Mixed(), $context)->shouldBeLike(new StringType());
    }


    function it_combines_the_types_and_normalizes_them_again_if_both_are_not_mixed(
        TypeConverter $converter,
        TypeNormalizer $normalizer
    ) {
        $context = new Fqsen('\\MyClass');

        $converter->convert(new String_(), $context)->willReturn(new StringType());
        $converter->convert(new Null_(), $context)->willReturn(new NullType());
        $normalizer->normalize(Argument::any())->willReturnArgument();
        $normalizer
            ->normalize(new ComposedType(new StringType(), new NullType()))
            ->willReturn(new NullableType(new StringType()));

        $this
            ->create(new String_(), new Null_(), $context)
            ->shouldBeLike(new NullableType(new StringType()));
    }
}
