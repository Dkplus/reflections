<?php
namespace spec\Dkplus\Reflection;

use Dkplus\Reflection\AutoloadingReflector;
use Dkplus\Reflection\Builder;
use Dkplus\Reflection\Type\TypeFactory;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Builder
 */
class BuilderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('create');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Builder::class);
    }

    function it_creates_a_type_factory()
    {
        $this->typeFactory()->shouldBeAnInstanceOf(TypeFactory::class);
    }

    function it_creates_a_reflector(TypeFactory $typeFactory)
    {
        $this->reflector($typeFactory)->shouldBeAnInstanceOf(AutoloadingReflector::class);
    }
}
