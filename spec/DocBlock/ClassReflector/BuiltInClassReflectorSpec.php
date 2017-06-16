<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\DocBlock\ClassReflector;

use Dkplus\Reflection\DocBlock\ClassReflector;
use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
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

    function it_reflects_classes_that_could_be_autoloaded()
    {
        $this->reflect(stdClass::class)->shouldBeLike(new ReflectionClass(stdClass::class));
    }

    function it_throws_an_ReflectionException_if_a_class_is_unknown()
    {
        $this
            ->shouldThrow(ReflectionException::class)
            ->during('reflect', ['NotExistingClass']);
    }
}
