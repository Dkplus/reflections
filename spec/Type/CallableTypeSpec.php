<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\CallableType;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflection\Mock\ClassReflectionStubBuilder;

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

    function it_allows_callables()
    {
        $this->allows(new CallableType())->shouldBe(true);
    }

    function it_allows_invokable_classes()
    {
        $this
            ->allows(new ClassType(ClassReflectionStubBuilder::build()->withInvokable(true)->finish()))
            ->shouldBe(true);
        $this
            ->allows(new ClassType(ClassReflectionStubBuilder::build()->withInvokable(false)->finish()))
            ->shouldBe(false);
    }

    function it_allows_no_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
