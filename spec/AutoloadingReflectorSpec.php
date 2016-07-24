<?php
namespace spec\Dkplus\Reflections;

use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\AutoloadingReflector;
use Dkplus\Reflections\ClassNotFound;
use Dkplus\Reflections\ClassReflection;
use Dkplus\Reflections\Reflector;
use PhpSpec\ObjectBehavior;

/**
 * @mixin AutoloadingReflector
 */
class AutoloadingReflectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AutoloadingReflector::class);
    }

    function it_implements_reflector()
    {
        $this->shouldImplement(Reflector::class);
    }

    function it_reflects_classes_from_class_name()
    {
        $this->reflectClass(Annotations::class)->shouldBeAReflectionOfClass(Annotations::class);
    }

    function it_throws_an_exception_if_it_could_not_find_a_class()
    {
        $this
            ->shouldThrow(ClassNotFound::named('ThisClassDoesNotExist'))
            ->during('reflectClass', ['ThisClassDoesNotExist']);
    }

    function it_allows_to_register_additional_psr4_paths()
    {
        $this->addPsr4Path('AnotherNamespace\\', __DIR__ . '/assets/');
        $this->reflectClass('AnotherNamespace\\TestClass')->shouldBeAReflectionOfClass('AnotherNamespace\\TestClass');
    }

    function it_allows_to_register_additional_files()
    {
        $this->addClassInFile('AnotherNamespace\\TestClass', __DIR__ . '/assets/TestClass.php');
        $this->reflectClass('AnotherNamespace\\TestClass')->shouldBeAReflectionOfClass('AnotherNamespace\\TestClass');
    }

    function getMatchers()
    {
        return [
            'beAReflectionOfClass' => function (ClassReflection $reflection, $className) {
                return $reflection->name() === $className;
            }
        ];
    }
}
