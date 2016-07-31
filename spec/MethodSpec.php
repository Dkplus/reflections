<?php
namespace spec\Dkplus\Reflections;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionMethod;
use BetterReflection\Reflection\ReflectionType;
use Dkplus\Reflections\Annotations;
use Dkplus\Reflections\Method;
use Dkplus\Reflections\Parameters;
use Dkplus\Reflections\Scanner\AnnotationScanner;
use Dkplus\Reflections\Type\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Method
 */
class MethodSpec extends ObjectBehavior
{
    function let(ReflectionMethod $reflectionMethod, Annotations $annotations, Parameters $parameters, Type $returnType)
    {
        $this->beConstructedWith($reflectionMethod, $annotations, $parameters, $returnType);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(Method::class);
    }

    function it_has_a_name(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getName()->willReturn('getMethodName');
        $this->name()->shouldBe('getMethodName');
    }

    function it_can_be_public(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isPublic()->willReturn(false);
        $this->isPublic()->shouldBe(false);

        $reflectionMethod->isPublic()->willReturn(true);
        $this->isPublic()->shouldBe(true);
    }

    function it_can_be_protected(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isProtected()->willReturn(false);
        $this->isProtected()->shouldBe(false);

        $reflectionMethod->isProtected()->willReturn(true);
        $this->isProtected()->shouldBe(true);
    }

    function it_can_be_private(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isPrivate()->willReturn(false);
        $this->isPrivate()->shouldBe(false);

        $reflectionMethod->isPrivate()->willReturn(true);
        $this->isPrivate()->shouldBe(true);
    }

    function it_can_be_static(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isStatic()->willReturn(false);
        $this->isStatic()->shouldBe(false);

        $reflectionMethod->isStatic()->willReturn(true);
        $this->isStatic()->shouldBe(true);
    }
    
    function it_can_be_final(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isFinal()->willReturn(false);
        $this->isFinal()->shouldBe(false);

        $reflectionMethod->isFinal()->willReturn(true);
        $this->isFinal()->shouldBe(true);
    }

    function it_can_be_abstract(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->isAbstract()->willReturn(false);
        $this->isAbstract()->shouldBe(false);

        $reflectionMethod->isAbstract()->willReturn(true);
        $this->isAbstract()->shouldBe(true);
    }

    function it_has_a_return_type(Type $returnType)
    {
        $this->returnType()->shouldBe($returnType);
    }

    function it_might_be_a_getter(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getNumberOfParameters()->willReturn(0);
        $reflectionMethod->getBodyCode()->willReturn('$this->myProperty = "test";');
        $this->isGetterOf('myProperty')->shouldBe(false);

        $reflectionMethod->getBodyCode()->willReturn('return (string) $this->myProperty;');
        $this->isGetterOf('myProperty')->shouldBe(true);

        $reflectionMethod->getBodyCode()->willReturn('return strval($this->myProperty);');
        $this->isGetterOf('myProperty')->shouldBe(true);

        $reflectionMethod->getNumberOfParameters()->willReturn(1);
        $this->isGetterOf('myProperty')->shouldBe(false);
    }

    function it_can_count_its_parameters(ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getNumberOfParameters()->willReturn(3);
        $this->countParameters()->shouldBe(3);
    }

    function it_has_parameters(Parameters $parameters)
    {
        $this->parameters()->shouldBe($parameters);
    }

    function it_might_types_to_be_passed(Parameters $parameters, Type $type)
    {
        $parameters->allows($type)->willReturn(true);
        $this->allowsToBePassed($type)->shouldBe(true);

        $parameters->allows($type)->willReturn(false);
        $this->allowsToBePassed($type)->shouldBe(false);
    }

    function it_has_annotations(Annotations $annotations)
    {
        $this->annotations()->shouldBe($annotations);
    }
}
