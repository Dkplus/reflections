<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\PropertyReflection;
use Dkplus\Reflection\Type\Type;
use PhpSpec\ObjectBehavior;
use ReflectionProperty;

class PropertyReflectionSpec extends ObjectBehavior
{
    function let(ReflectionProperty $reflectionProperty, Type $type, Annotations $annotations)
    {
        $this->beConstructedWith($reflectionProperty, $type, $annotations);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyReflection::class);
    }

    function it_has_a_name(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->getName()->willReturn('foo');
        $this->name()->shouldBe('foo');
    }

    function it_may_allow_a_type(Type $type, Type $anotherType)
    {
        $type->accepts($anotherType)->willReturn(true);
        $this->allows($anotherType)->shouldBe(true);

        $type->accepts($anotherType)->willReturn(false);
        $this->allows($anotherType)->shouldBe(false);
    }

    function it_can_be_public(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->isPublic()->willReturn(false);
        $this->isPublic()->shouldBe(false);

        $reflectionProperty->isPublic()->willReturn(true);
        $this->isPublic()->shouldBe(true);
    }

    function it_can_be_protected(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->isProtected()->willReturn(false);
        $this->isProtected()->shouldBe(false);

        $reflectionProperty->isProtected()->willReturn(true);
        $this->isProtected()->shouldBe(true);
    }

    function it_can_be_private(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->isPrivate()->willReturn(false);
        $this->isPrivate()->shouldBe(false);

        $reflectionProperty->isPrivate()->willReturn(true);
        $this->isPrivate()->shouldBe(true);
    }

    function it_can_be_static(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->isStatic()->willReturn(false);
        $this->isStatic()->shouldBe(false);

        $reflectionProperty->isStatic()->willReturn(true);
        $this->isStatic()->shouldBe(true);
    }

    function it_has_annotations(Annotations $annotations)
    {
        $this->docBlock()->shouldBe($annotations);
    }
}
