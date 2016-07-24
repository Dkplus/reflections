<?php
namespace spec\Dkplus\Reflections;

use Dkplus\Reflections\Parameters;
use PhpSpec\ObjectBehavior;

class ParametersSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Parameters::class);
    }
}
