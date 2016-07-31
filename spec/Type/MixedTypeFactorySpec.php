<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Reflector;
use Dkplus\Reflections\Type\MixedType;
use Dkplus\Reflections\Type\MixedTypeFactory;
use Dkplus\Reflections\Type\TypeFactory;
use phpDocumentor\Reflection\Types\Mixed;
use PhpSpec\ObjectBehavior;

/**
 * @mixin MixedTypeFactory
 */
class MixedTypeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MixedTypeFactory::class);
    }

    function it_is_a_type_factory()
    {
        $this->shouldImplement(TypeFactory::class);
    }

    function it_creates_mixed_types(Reflector $reflector)
    {
        $this->create($reflector, new Mixed(), [], true)->shouldBeAnInstanceOf(MixedType::class);
    }
}
