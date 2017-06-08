<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\ReflectorStrategy;

use Dkplus\Reflection\ReflectorStrategy\BuiltInClassReflector;
use Dkplus\Reflection\Type\ClassReflector;
use PhpSpec\ObjectBehavior;
use ReflectionClass;
use ReflectionException;
use stdClass;

class BuiltInClassReflectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BuiltInClassReflector::class);
    }

    function it_is_a_ClassReflector()
    {
        $this->shouldImplement(ClassReflector::class);
    }

    function it_reflects_an_autoloaded_class()
    {
        $this->reflect(stdClass::class)->shouldBeLike(new ReflectionClass(stdClass::class));
    }

    function it_throws_a_ReflectionException_if_a_class_does_not_exist()
    {
        $this->shouldThrow(ReflectionException::class)->during('reflect', ['UnknownClass']);
    }
}
