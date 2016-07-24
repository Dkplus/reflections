<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\ClassType;
use Dkplus\Reflections\Type\ObjectType;
use Dkplus\Reflections\Type\Type;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\Reflections\Mock\ClassReflectionStubBuilder;

/**
 * @mixin ObjectType
 */
class ObjectTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ObjectType::class);
    }

    function it_is_a_type()
    {
        $this->shouldImplement(Type::class);
    }

    function it_has_a_string_representation()
    {
        $this->__toString()->shouldBe('object');
    }

    function it_allows_objects_to_be_passed()
    {
        $this->allows(new ObjectType())->shouldBe(true);
    }

    function it_allows_classes_to_be_passed()
    {
        $this->allows(new ClassType(ClassReflectionStubBuilder::build()->finish()));
    }

    function it_does_not_allow_other_types(Type $type)
    {
        $this->allows($type)->shouldBe(false);
    }
}
