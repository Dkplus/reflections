<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionProperty;
use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\PropertyReflection;
use Dkplus\Reflections\Scanner\AnnotationScanner;
use Doctrine\Common\Annotations\Annotation\Target;
use PhpSpec\ObjectBehavior;

/**
 * @mixin PropertyReflection
 */
class PropertyReflectionSpec extends ObjectBehavior
{
    private $imports = [
        'Target' => 'Doctrine\\Common\\Annotations\\Annotation\\Target',
    ];

    private $fileName = '/var/www/MyClass.php';

    function let(ReflectionProperty $reflectionProperty, ReflectionClass $class, AnnotationScanner $annotations)
    {
        $this->beConstructedWith($reflectionProperty, $annotations, $this->imports);

        $class->getFileName()->willReturn($this->fileName);
        $reflectionProperty->getDeclaringClass()->willReturn($class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyReflection::class);
    }

    function it_has_a_name(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->getName()->willReturn('My\\BarClass');
        $this->name()->shouldBe('My\\BarClass');
    }

    function it_can_have_multiple_types(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->getDocBlockTypeStrings()->willReturn(['string', 'null']);
        $this->types()->shouldBe(['string', 'null']);
    }

    function its_first_defined_type_is_its_main_type(ReflectionProperty $reflectionProperty)
    {
        $reflectionProperty->getDocBlockTypeStrings()->willReturn(['string', 'null']);
        $this->mainType()->shouldBe('string');
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

    function it_might_have_annotations(ReflectionProperty $reflectionProperty, AnnotationScanner $annotations)
    {
        $docBlock = <<<'BLOCK'
/**
 * @Target("METHOD")
 */
BLOCK;
        $expectedAnnotations = [new Target(['value' => "CLASS"])];

        $reflectionProperty->getDocComment()->willReturn($docBlock);
        $annotations
            ->scanForAnnotations($docBlock, $this->fileName, $this->imports)
            ->willReturn(new Annotations($expectedAnnotations));
        $this->annotations()->shouldBeLike(new Annotations($expectedAnnotations));
    }
}
