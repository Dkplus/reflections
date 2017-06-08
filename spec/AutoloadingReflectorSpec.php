<?php
namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\AutoloadingReflectorStrategy;
use Dkplus\Reflection\ClassReflection_;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\Factory\TypeFactory;
use PhpSpec\ObjectBehavior;

/**
 * @mixin AutoloadingReflectorStrategy
 */
class AutoloadingReflectorSpec extends ObjectBehavior
{
    function let(TypeFactory $typeFactory)
    {
        $this->beConstructedWith($typeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AutoloadingReflectorStrategy::class);
    }

    function it_implements_reflector()
    {
        $this->shouldImplement(ReflectorStrategy::class);
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
            'beAReflectionOfClass' => function (ClassReflection_ $reflection, $className) {
                return $reflection->name() === $className;
            }
        ];
    }
}
