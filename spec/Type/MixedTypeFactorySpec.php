<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\MixedTypeFactory;
use Dkplus\Reflection\Type\TypeFactory;
use phpDocumentor\Reflection\Types\Mixed;
use PhpSpec\ObjectBehavior;

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

    function it_creates_mixed_types(ReflectorStrategy $reflector)
    {
        $this->create($reflector, new Mixed(), [], true)->shouldBeAnInstanceOf(MixedType::class);
    }
}
