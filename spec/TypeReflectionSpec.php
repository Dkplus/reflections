<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionType;
use Dkplus\Reflections\ClassNotFound;
use Dkplus\Reflections\ClassReflection;
use Dkplus\Reflections\Reflector;
use Dkplus\Reflections\TypeReflection;
use phpDocumentor\Reflection\Type;
use PhpSpec\ObjectBehavior;

/**
 * @mixin TypeReflection
 */
class TypeReflectionSpec extends ObjectBehavior
{/*
    function let(Reflector $reflections, ReflectionType $reflectionType, Type $type)
    {
        $reflectionType->getTypeObject()->willReturn($type);
        $this->beConstructedWith($reflections, $reflectionType, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeReflection::class);
    }

    function it_might_be_mixed(Type $type)
    {
        $type->__toString()->willReturn('mixed');

        $this->__toString()->shouldBe('mixed');
        $this->allows('bool')->shouldBe(true);
        $this->allows('void')->shouldBe(false);
        $this->allows('null')->shouldBe(true);
        $this->allows('stdClass')->shouldBe(true);
        $this->allows('array')->shouldBe(true);
        $this->allows('stdClass[]')->shouldBe(true);
        $this->allows('array<stdClass>')->shouldBe(true);
    }

    function it_might_be_mixed_because_of_no_type_detected()
    {

    }

    function it_might_be_a_scalar(Type $type)
    {
        $type->__toString()->willReturn('string');

        $this->__toString()->shouldBe('string');
        $this->allows('string')->shouldBe(true);
        $this->allows('bool')->shouldBe(false);
    }

    function it_might_be_a_callable(
        Type $type,
        Reflector $reflections,
        ClassReflection $invokableClass,
        ClassReflection $nonInvokableClass
    ) {
        $type->__toString()->willReturn('callable');
        $invokableClass->isInvokable()->willReturn(true);
        $nonInvokableClass->isInvokable()->willReturn(false);
        $reflections->reflectClass('InvokableClass')->willReturn($invokableClass);
        $reflections->reflectClass('NonInvokableClass')->willReturn($nonInvokableClass);
        $reflections->reflectClass('NonExistingClass')->willThrow(ClassNotFound::named('NonExistingClass'));

        $this->__toString()->shouldBe('callable');
        $this->allows('callable')->shouldBe(true);
        $this->allows('string')->shouldBe(false);
        $this->allows('array')->shouldBe(true);
        $this->allows('InvokableClass')->shouldBe(true);
        $this->allows('NonInvokableClass')->shouldBe(false);
        $this->allows('NonExistingClass')->shouldBe(false);
        $this->allows('stdClass[]')->shouldBe(false);
    }

    function it_might_be_a_resource(Type $type)
    {
        $type->__toString()->willReturn('resource');

        $this->__toString()->shouldBe('resource');
        $this->allows('resource')->shouldBe(true);
        $this->allows('string')->shouldBe(false);
    }

    function it_might_be_a_class_instance(
        Type $type,
        Reflector $reflections,
        ClassReflection $subClass,
        ClassReflection $implementedInterface,
        ClassReflection $nonSubClass
    ) {
        $type->__toString()->willReturn('MyClass');

        $implementedInterface->name()->willReturn('ClassImplementing\\TheInterface\\MyClass');
        $implementedInterface->implementsInterface('MyClass')->willReturn(true);
        $implementedInterface->isSubclassOf('MyClass')->willReturn(false);
        $reflections->reflectClass('ClassImplementing\\TheInterface\\MyClass')->willReturn($implementedInterface);

        $subClass->name()->willReturn('SubClass\\Of\\MyClass');
        $subClass->implementsInterface('MyClass')->willReturn(false);
        $subClass->isSubclassOf('MyClass')->willReturn(true);
        $reflections->reflectClass('SubClass\\Of\\MyClass')->willReturn($subClass);

        $nonSubClass->name()->willReturn('A\\Non\\Related\\Class');
        $nonSubClass->implementsInterface('MyClass')->willReturn(false);
        $nonSubClass->isSubclassOf('MyClass')->willReturn(false);
        $reflections->reflectClass('A\\Non\\Related\\Class')->willReturn($nonSubClass);

        $this->__toString()->shouldBe('MyClass');
        $this->allows('string')->shouldBe(false);
        $this->allows('array')->shouldBe(false);
        $this->allows('MyClass')->shouldBe(true);
        $this->allows('ClassImplementing\\TheInterface\\MyClass')->shouldBe(true);
        $this->allows('SubClass\\Of\\MyClass')->shouldBe(true);
        $this->allows('A\\Non\\Related\\Class')->shouldBe(false);
        $this->allows('stdClass[]')->shouldBe(false);
    }

    function it_might_be_a_mixed_array(Reflector $reflections, ReflectionType $reflectionType, Type $type)
    {
        $type->__toString()->willReturn('array');
        $this->beConstructedWith($reflections, $reflectionType, ['array']);

        $this->__toString()->shouldBe('array');
        $this->allows('array')->shouldBe(true);
        $this->allows('stdClass')->shouldBe(false);
        $this->allows('string')->shouldBe(false);
        $this->allows('string[]')->shouldBe(false);
        $this->allows('array<string>')->shouldBe(true);
        $this->allows('array<stdClass>')->shouldBe(true);
        $this->allows('array<callable>')->shouldBe(true);
        $this->allows('array<resource>')->shouldBe(true);
    }

/*    function it_have_a_string_representation(Type $type)
    {
        $type->__toString()->willReturn('string');
        $this->__toString()->shouldBe('string');
    }

    function its_string_representation_can_be_mixed(Reflections $reflections)
    {
        $this->beConstructedWith($reflections, null, []);
        $this->__toString()->shouldBe('mixed');
    }

    function it_might_allow_null(ReflectionType $type)
    {
        $type->allowsNull()->willReturn(true);
        $this->allows('null')->shouldBe(true);

        $type->allowsNull()->willReturn(false);
        $this->allows('null')->shouldBe(false);
    }

    function it_allows_null_if_its_string_representation_is_mixed(Reflections $reflections)
    {
        $this->beConstructedWith($reflections, null, []);
        $this->allows('null')->shouldBe(true);
    }

    function it_might_be_an_array(ReflectionType $type)
    {
        $type->getTypeObject()->willReturn(new Array_());
        $this->__toString()->shouldBe('array');

        $this->allows('string')->shouldBe(false);
        $this->allows('void')->shouldBe(false);
        $this->allows('stdClass')->shouldBe(false);
        $this->allows('resource')->shouldBe(false);
        $this->allows('callable')->shouldBe(false);
        $this->allows('array')->shouldBe(true);
        $this->allows('stdClass[]')->shouldBe(true);
        $this->allows('string[]')->shouldBe(true);
    }

    function it_might_be_a_specified_array(ReflectionType $type)
    {
        $type->getTypeObject()->willReturn(new Array_());
        $this->beConstructedWith($type, ['string[]', 'array']);

        $this->__toString()->shouldBe('string[]');
    }

    function it_might_be_void()
    {
        $this->beConstructedWith(null, ['void']);

        $this->__toString()->shouldBe('void');
        $this->allows('string')->shouldBe(false);
        $this->allows('void')->shouldBe(false);
        $this->allows('stdClass')->shouldBe(false);
        $this->allows('resource')->shouldBe(false);
        $this->allows('callable')->shouldBe(false);
        $this->allows('array')->shouldBe(false);
        $this->allows('stdClass[]')->shouldBe(false);
    }*/
}
