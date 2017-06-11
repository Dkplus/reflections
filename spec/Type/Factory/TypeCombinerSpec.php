<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\Factory\TypeCombiner;
use PhpSpec\ObjectBehavior;

class TypeCombinerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypeCombiner::class);
    }


}
