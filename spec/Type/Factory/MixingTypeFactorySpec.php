<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\MixedType;
use phpDocumentor\Reflection\Types\Mixed;
use PhpSpec\ObjectBehavior;

class MixingTypeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MixingTypeFactorySpec::class);
    }

    function it_returns_mixed_if_both_types_are_mixed()
    {
        return $this->create(new Mixed(), new Mixed())->shouldBeLike(new MixedType());
    }
}
