<?php
namespace spec\Dkplus\Reflection;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\BetterReflectionClassReflection;
use Dkplus\Reflection\Reflector;
use Dkplus\Reflection\Scanner\AnnotationScanner;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Type\StringType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\TypeFactory;
use Doctrine\Common\Annotations\Annotation\Target;
use phpDocumentor\Reflection\Types\Mixed;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function let(
        ReflectionClass $reflectionClass,
        AnnotationScanner $annotations,
        TypeFactory $typeFactory,
        Reflector $reflector
    ) {
        $this->beConstructedWith($reflectionClass, $annotations, $reflector, $typeFactory, $this->imports);

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

    function it_has_properties(
        ReflectionClass $reflectionClass,
        ReflectionProperty $property,
        AnnotationScanner $annotations,
        TypeFactory $typeFactory
    ) {
        $reflectionClass->getName()->willReturn('MyClass');
        $property->getName()->willReturn('id');
        $property->getDocBlockTypeStrings()->willReturn(['string']);
        $property->getDocComment()->willReturn('/** @Target("CLASS") */');

        $typeFactory
            ->create(Argument::any(), Argument::type(Mixed::class), ['string'], false)
            ->willReturn(new StringType());

        $reflectionClass->getProperties()->willReturn([$property]);

        $annotations
            ->scanForAnnotations('/** @Target("CLASS") */', '/var/www/MyClass.php', $this->imports)
            ->willReturn(new Annotations([new Target(['value' => "CLASS"])]));

        $this->properties()->shouldHaveSize(1);
    }

    function it_has_methods(
        ReflectionClass $reflectionClass,
        ReflectionMethod $method,
        AnnotationScanner $annotations,
        TypeFactory $typeFactory,
        Type $type,
        Annotations $methodAnnotations
    ) {
        $reflectionClass->getName()->willReturn('MyClass');
        $method->getName()->willReturn('__invoke');
        $method->getReturnType()->willReturn(null);
        $method->getDocBlockReturnTypes()->willReturn([]);
        $method->getParameters()->willReturn([]);
        $method->getDocComment()->willReturn('');
        $reflectionClass->getMethods()->willReturn([$method]);

        $annotations
            ->scanForAnnotations(Argument::any(), Argument::any(), $this->imports)
            ->willReturn($methodAnnotations);

        $typeFactory->create(Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn($type);

        $this->methods()->shouldHaveSize(1);
    }

    function it_might_be_invokable(
        ReflectionClass $reflectionClass,
        ReflectionMethod $method,
        AnnotationScanner $annotations,
        TypeFactory $typeFactory,
        Type $type,
        Annotations $methodAnnotations
    ) {
        $reflectionClass->getName()->willReturn('MyClass');
        $method->getName()->willReturn('__invoke');
        $method->getReturnType()->willReturn(null);
        $method->getDocBlockReturnTypes()->willReturn([]);
        $method->getParameters()->willReturn([]);
        $method->getDocComment()->willReturn('');
        $reflectionClass->getMethods()->willReturn([$method]);

        $annotations
            ->scanForAnnotations(Argument::any(), Argument::any(), $this->imports)
            ->willReturn($methodAnnotations);

        $typeFactory->create(Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn($type);

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
