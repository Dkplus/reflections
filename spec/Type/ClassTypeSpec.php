<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use ArrayObject;
use DateTimeImmutable;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use Serializable;
use stdClass;

class ClassTypeSpec extends ObjectBehavior
{
    function let(ReflectionClass $reflection)
    {
        $reflection->getName()->willReturn('MyClass');
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

    function it_knows_whether_its_invokable(ReflectionClass $reflection)
    {
        $reflection->hasMethod('__invoke')->willReturn(true);
        $this->isInvokable()->shouldBe(true);

        $reflection->hasMethod('__invoke')->willReturn(false);
        $this->isInvokable()->shouldBe(false);
    }

    function it_has_a_className()
    {
        $this->className()->shouldBe('\\MyClass');
    }

    function its_string_representation_is_its_class_name()
    {
        $this->__toString()->shouldBe('\\MyClass');
    }

    function it_allows_objects_of_the_same_class(ReflectionClass $reflection)
    {
        $this->accepts(new ClassType($reflection->getWrappedObject()))->shouldBe(true);
    }

    function it_allows_objects_that_implement_it(ReflectionClass $anotherReflection)
    {
        $anotherReflection->getName()->willReturn('AnotherClass');
        $anotherReflection->isSubclassOf(Argument::any())->willReturn(false);
        $anotherReflection->implementsInterface('\\MyClass')->willReturn(true);
        $anotherClass = new ClassType($anotherReflection->getWrappedObject());

        $this->accepts($anotherClass)->shouldBe(true);

        $anotherReflection->implementsInterface('\\MyClass')->willReturn(false);
        $this->accepts($anotherClass)->shouldBe(false);
    }

    function it_allows_objects_that_extend_it(ReflectionClass $anotherReflection)
    {
        $anotherReflection->getName()->willReturn('AnotherClass');
        $anotherReflection->implementsInterface(Argument::any())->willReturn(false);
        $anotherReflection->isSubclassOf('\\MyClass')->willReturn(true);
        $anotherClass = new ClassType($anotherReflection->getWrappedObject());

        $this->accepts($anotherClass)->shouldBe(true);

        $anotherReflection->isSubclassOf('\\MyClass')->willReturn(false);
        $this->accepts($anotherClass)->shouldBe(false);
    }

    function it_knows_whether_its_implements_or_extends_one_class(ReflectionClass $reflection)
    {
        $reflection->implementsInterface(Argument::any())->willReturn(false);
        $reflection->isSubclassOf(Argument::any())->willReturn(false);
        $reflection->implementsInterface(Serializable::class)->willReturn(true);
        $reflection->implementsInterface(DateTimeImmutable::class)->willReturn(false);
        $reflection->isSubclassOf(stdClass::class)->willReturn(true);
        $reflection->isSubclassOf(ArrayObject::class)->willReturn(false);

        $this->implementsOrIsSubClassOf(Serializable::class)->shouldBe(true);
        $this->implementsOrIsSubClassOf(DateTimeImmutable::class)->shouldBe(false);
        $this->implementsOrIsSubClassOf(stdClass::class)->shouldBe(true);
        $this->implementsOrIsSubClassOf(ArrayObject::class)->shouldBe(false);
    }

    function it_does_not_allow_other_types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
