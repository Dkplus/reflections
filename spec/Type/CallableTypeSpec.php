<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\CallableType;
use PhpSpec\ObjectBehavior;

class CallableTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CallableType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('callable');
    }

    function it_accepts_callables()
    {
        $this->accepts(new CallableType())->shouldBe(true);
    }

    function it_accepts_invokable_classes(ClassType $invokable, ClassType $nonInvokable)
    {
        $invokable->isInvokable()->willReturn(true);
        $nonInvokable->isInvokable()->willReturn(false);

        $this->accepts($invokable)->shouldBe(true);
        $this->accepts($nonInvokable)->shouldBe(false);
    }

    function it_does_not_accept_other_types(Type $type)
    {
        $this->accepts($type)->shouldBe(false);
    }
}
