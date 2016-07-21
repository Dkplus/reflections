<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\Properties;
use Dkplus\Reflections\PropertyReflection;
use Dkplus\Reflections\Scanner\AnnotationScanner;
use Dkplus\Reflections\ClassReflection;
use Doctrine\Common\Annotations\Annotation\Target;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ClassReflection
 */
class ClassReflectionSpec extends ObjectBehavior
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
        $this->shouldHaveType(ClassReflection::class);
    }

    function it_might_be_final(ReflectionClass $reflectionClass)
    {
        $reflectionClass->isFinal()->willReturn(true);
        $this->isFinal()->shouldBe(true);

        $reflectionClass->isFinal()->willReturn(false);
        $this->isFinal()->shouldBe(false);
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

        $this->properties()->shouldContainOneProperty();
    }

    function getMatchers()
    {
        return [
            'containOneProperty' => function (Properties $subject) {
                return $subject->size() === 1;
            }
        ];
    }
}
