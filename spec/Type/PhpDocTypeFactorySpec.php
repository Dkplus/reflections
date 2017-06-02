<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\BooleanType;
use Dkplus\Reflection\Type\CallableType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\CollectionType;
use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\FloatType;
use Dkplus\Reflection\Type\IntegerType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\ObjectType;
use Dkplus\Reflection\Type\PhpDocTypeFactory;
use Dkplus\Reflection\Type\ResourceType;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\TypeFactory;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Types\Mixed;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Dkplus\Reflection\Mock\ClassReflectionStubBuilder;
use Traversable;


class PhpDocTypeFactorySpec extends ObjectBehavior
{
    function let(TypeFactory $decorated)
    {
        $this->beConstructedWith($decorated);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PhpDocTypeFactory::class);
    }

    function it_is_a_type_factory()
    {
        $this->shouldImplement(TypeFactory::class);
    }

    function it_creates_a_string_type_if_a_phpdoc_string_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['string'], false)->shouldBeAnInstanceOf(StringType::class);
    }

    function it_creates_an_integer_type_if_a_phpdoc_integer_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['integer'], false)->shouldBeAnInstanceOf(IntegerType::class);
        $this->create($reflector, new Mixed(), ['int'], false)->shouldBeAnInstanceOf(IntegerType::class);
    }

    function it_creates_a_float_type_if_a_phpdoc_float_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['float'], false)->shouldBeAnInstanceOf(FloatType::class);
        $this->create($reflector, new Mixed(), ['double'], false)->shouldBeAnInstanceOf(FloatType::class);
    }

    function it_creates_a_bool_type_if_a_phpdoc_boolean_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['boolean'], false)->shouldBeAnInstanceOf(BooleanType::class);
        $this->create($reflector, new Mixed(), ['bool'], false)->shouldBeAnInstanceOf(BooleanType::class);
        $this->create($reflector, new Mixed(), ['Bool'], false)->shouldBeAnInstanceOf(BooleanType::class);
    }

    function it_creates_a_callable_if_a_phpdoc_callable_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['callable'], false)->shouldBeAnInstanceOf(CallableType::class);
        $this->create($reflector, new Mixed(), ['callback'], false)->shouldBeAnInstanceOf(CallableType::class);
    }

    function it_creates_a_resource_type_if_a_phpdoc_resource_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['resource'], false)->shouldBeAnInstanceOf(ResourceType::class);
    }

    function it_creates_a_void_type_if_a_phpdoc_void_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['void'], false)->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_creates_an_object_type_if_a_phpdoc_object_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['object'], false)->shouldBeAnInstanceOf(ObjectType::class);
    }

    function it_creates_an_array_type_if_a_phpdoc_array_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['array'], false)->shouldBeAnInstanceOf(ArrayType::class);
    }

    function it_creates_a_class_type_if_a_class_phpdoc_is_given(ReflectorStrategy $reflector)
    {
        $classReflection = ClassReflectionStubBuilder::build()->withClassName('stdClass')->finish();
        $reflector->reflectClass('stdClass')->willReturn($classReflection);

        $this->create($reflector, new Mixed(), ['stdClass'], false)->shouldBeAReflectionOfClass('stdClass');
    }

    function it_creates_an_iterable_type_if_a_phpdoc_iterable_is_given(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), ['iterable'], false)->shouldBeAnInstanceOf(IterableType::class);
    }

    function it_let_the_decorated_factory_create_the_type_if_no_phpdocs_are_given(
        ReflectorStrategy $reflector,
        TypeFactory $decorated,
        Type $type
    ) {
        $decorated->create($reflector, Argument::any(), Argument::any(), Argument::any())->willReturn($type);
        $this->create($reflector, new Mixed(), [], false)->shouldBe($type);
    }

    function it_creates_a_combined_type_if_multiple_phpdoc_types_are_given(ReflectorStrategy $reflector)
    {
        $this
            ->create($reflector, new Mixed(), ['string', 'int'], false)
            ->shouldBeComposedOf(StringType::class, IntegerType::class);
    }

    function it_creates_traversables_if_only_iterable_phpdoc_types_are_given(ReflectorStrategy $reflector)
    {
        $this
            ->create($reflector, new Mixed(), ['string[]'], false)
            ->shouldBeAnIterableOf(StringType::class);

        $this
            ->create($reflector, new Mixed(), ['string[]', 'int[]'], false)
            ->shouldBeAnIterableOf(StringType::class, IntegerType::class);
    }

    function it_creates_a_typed_array_type_if_only_iterable_phpdoc_types_and_an_array_phpdoc_type_are_given(
        ReflectorStrategy $reflector
    ) {
        $this
            ->create($reflector, new Mixed(), ['string[]', 'array'], false)
            ->shouldBeAnArrayOf(StringType::class);

        $this
            ->create($reflector, new Mixed(), ['string[]', 'int[]', 'array'], false)
            ->shouldBeAnArrayOf(StringType::class, IntegerType::class);
    }

    function it_creates_a_collection_if_only_iterable_phpdoc_types_and_a_traversable_class_type_are_given(
        ReflectorStrategy $reflector
    ) {
        $traversableClass = ClassReflectionStubBuilder::build()->implement(Traversable::class)->finish();
        $nonTraversableClass = ClassReflectionStubBuilder::build()->finish();

        $reflector->reflectClass('Collection')->willReturn($traversableClass);
        $reflector->reflectClass('stdClass')->willReturn($nonTraversableClass);

        $this
            ->create($reflector, new Mixed(), ['string[]', 'Collection'], false)
            ->shouldBeACollectionOf(StringType::class);

        $this
            ->create($reflector, new Mixed(), ['string[]', 'int[]', 'Collection'], false)
            ->shouldBeACollectionOf(StringType::class, IntegerType::class);

        $this
            ->create($reflector, new Mixed(), ['string[]', 'stdClass'], false)
            ->shouldBeAnInstanceOf(ComposedType::class);
    }

    public function getMatchers()
    {
        return [
            'beAReflectionOfClass' => function (Type $subject, string $expectedClass) {
                return $subject instanceof ClassType && $subject->reflection()->name() === $expectedClass;
            },
            'beAnArrayOf' => function (Type $subject, string ...$expectedTypes) {
                if (! $subject instanceof ArrayType) {
                    return false;
                }
                if (count($expectedTypes) === 1) {
                    $expectedType = current($expectedTypes);
                    return $subject->decoratedType() instanceof $expectedType;
                }
                if (! $subject->decoratedType() instanceof ComposedType) {
                    return false;
                }
                foreach ($subject->decoratedType() as $i => $each) {
                    if (! $each instanceof $expectedTypes[$i]) {
                        return false;
                    }
                }
                return true;
            },
            'beAnIterableOf' => function (Type $subject, string ...$expectedTypes) {
                if (! $subject instanceof IterableType) {
                    return false;
                }
                if (count($expectedTypes) === 1) {
                    $expectedType = current($expectedTypes);
                    return $subject->decoratedType() instanceof $expectedType;
                }
                if (! $subject->decoratedType() instanceof ComposedType) {
                    return false;
                }
                foreach ($subject->decoratedType() as $i => $each) {
                    if (! $each instanceof $expectedTypes[$i]) {
                        return false;
                    }
                }
                return true;
            },
            'beACollectionOf' => function (Type $subject, string ...$expectedTypes) {
                if (! $subject instanceof CollectionType) {
                    return false;
                }
                if (count($expectedTypes) === 1) {
                    $expectedType = current($expectedTypes);
                    return $subject->decoratedType() instanceof $expectedType;
                }
                if (! $subject->decoratedType() instanceof ComposedType) {
                    return false;
                }
                foreach ($subject->decoratedType() as $i => $each) {
                    if (! $each instanceof $expectedTypes[$i]) {
                        return false;
                    }
                }
                return true;
            },
            'beComposedOf' => function (Type $subject, string ...$expectedClasses) {
                if (! $subject instanceof ComposedType) {
                    return false;
                }
                foreach ($subject->decoratedTypes() as $i => $each) {
                    if (! $each instanceof $expectedClasses[$i]) {
                        return false;
                    }
                }
                return true;
            },
        ];
    }
}
