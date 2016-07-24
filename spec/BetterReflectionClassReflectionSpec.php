<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\BetterReflectionClassReflection;
use Dkplus\Reflections\Scanner\AnnotationScanner;
use Dkplus\Reflections\ClassReflection;
use Doctrine\Common\Annotations\Annotation\Target;
use PhpSpec\ObjectBehavior;

/**
 * @mixin BetterReflectionClassReflection
 */
class BetterReflectionClassReflectionSpec extends ObjectBehavior
{
    private $imports = [
        'Target' => 'Doctrine\\Common\\Annotations\\Annotation\\Target',
        'Enum' => 'Doctrine\\Common\\Annotations\\Annotation\\Enum',
    ];
    private $fileName = '/var/www/MyClass.php';

    function let(ReflectionClass $reflectionClass, AnnotationScanner $annotations)
    {
        $this->beConstructedWith($reflectionClass, $annotations, $this->imports);

        $reflectionClass->getFileName()->willReturn($this->fileName);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BetterReflectionClassReflection::class);
    }

    function it_is_a_class_reflection()
    {
        $this->shouldImplement(ClassReflection::class);
    }

    function it_might_be_final(ReflectionClass $reflectionClass)
    {
        $reflectionClass->isFinal()->willReturn(true);
        $this->isFinal()->shouldBe(true);

        $reflectionClass->isFinal()->willReturn(false);
        $this->isFinal()->shouldBe(false);
    }

    function it_might_be_cloneable(ReflectionClass $reflectionClass)
    {
        $reflectionClass->isCloneable()->willReturn(true);
        $this->isCloneable()->shouldBe(true);

        $reflectionClass->isCloneable()->willReturn(false);
        $this->isCloneable()->shouldBe(false);
    }

    function it_might_be_abstract(ReflectionClass $reflectionClass)
    {
        $reflectionClass->isAbstract()->willReturn(false);
        $this->isAbstract()->shouldBe(false);

        $reflectionClass->isAbstract()->willReturn(true);
        $this->isAbstract()->shouldBe(true);
    }

    function it_might_have_annotations(ReflectionClass $reflectionClass, AnnotationScanner $annotations)
    {
        $docBlock = <<<'BLOCK'
/**
 * @Target("CLASS")
 */
BLOCK;
        $expectedAnnotations = [new Target(['value' => "CLASS"])];

        $reflectionClass->getDocComment()->willReturn($docBlock);
        $annotations
            ->scanForAnnotations($docBlock, $this->fileName, $this->imports)
            ->willReturn(new Annotations($expectedAnnotations));
        $this->annotations()->shouldBeLike(new Annotations($expectedAnnotations));
    }

    function it_provides_the_class_name(ReflectionClass $reflectionClass)
    {
        $reflectionClass->getName()->willReturn(Annotations::class);

        $this->name()->shouldBe(Annotations::class);
    }

    function it_has_a_file_name(ReflectionClass $reflectionClass)
    {
        $reflectionClass->getFileName()->willReturn('/var/www/Bar.php');
        $this->fileName()->shouldBe('/var/www/Bar.php');
    }

    function it_has_properties(ReflectionClass $reflectionClass, ReflectionProperty $property)
    {
        $reflectionClass->getName()->willReturn('MyClass');
        $property->getName()->willReturn('id');
        $reflectionClass->getProperties()->willReturn([$property]);

        $this->properties()->shouldHaveSize(1);
    }

    function it_has_methods(ReflectionClass $reflectionClass, ReflectionMethod $method)
    {
        $reflectionClass->getName()->willReturn('MyClass');
        $method->getName()->willReturn('getId');
        $reflectionClass->getMethods()->willReturn([$method]);

        $this->methods()->shouldHaveSize(1);
    }

    function it_might_be_invokable(ReflectionClass $reflectionClass, ReflectionMethod $method)
    {
        $reflectionClass->getName()->willReturn('MyClass');
        $method->getName()->willReturn('__invoke');
        $reflectionClass->getMethods()->willReturn([$method]);

        $this->isInvokable()->shouldBe(true);

        $reflectionClass->getMethods()->willReturn([]);

        $this->isInvokable()->shouldBe(false);
    }

    function it_might_be_a_subclass_of_another_class(ReflectionClass $reflectionClass)
    {
        $reflectionClass->isSubclassOf('stdClass')->willReturn(false);

        $this->isSubclassOf('stdClass')->shouldBe(false);

        $reflectionClass->isSubclassOf('stdClass')->willReturn(true);

        $this->isSubclassOf('stdClass')->shouldBe(true);
    }

    function it_might_implement_an_interface(ReflectionClass $reflectionClass)
    {
        $reflectionClass->implementsInterface('MyInterface')->willReturn(false);

        $this->implementsInterface('MyInterface')->shouldBe(false);

        $reflectionClass->implementsInterface('MyInterface')->willReturn(true);

        $this->implementsInterface('MyInterface')->shouldBe(true);
    }

    function getMatchers()
    {
        return [
            'haveSize' => function ($subject, int $expected) {
                return $subject->size() === $expected;
            }
        ];
    }
}
