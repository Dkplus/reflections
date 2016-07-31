<?php

namespace spec\Dkplus\Reflections;

use Dkplus\Reflections\Method;
use Dkplus\Reflections\Methods;
use Dkplus\Reflections\MissingMethod;
use PhpSpec\ObjectBehavior;

class MethodsSpec extends ObjectBehavior
{
    function let(Method $method)
    {
        $method->name()->willReturn('getFoo');
        $this->beConstructedWith('MyClass', [$method]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Methods::class);
    }

    function it_has_a_size()
    {
        $this->size()->shouldBe(1);
    }

    function it_contains_methods()
    {
        $this->contains('getFoo')->shouldBe(true);
        $this->contains('getBar')->shouldBe(false);
    }

    function it_provides_all_methods(Method $method)
    {
        $this->all()->shouldBeLike([$method]);
    }

    function it_provides_a_method_by_name(Method $method)
    {
        $this->named('getFoo')->shouldBe($method);

        $this->shouldThrow(MissingMethod::inClass('getBar', 'MyClass'))->during('named', ['getBar']);
    }

    function it_knows_whether_one_method_is_a_getter(Method $method)
    {
        $method->isGetterOf('foo')->willReturn(true);
        $method->isGetterOf('bar')->willReturn(false);
        $this->containsGetterFor('foo')->shouldBe(true);
        $this->containsGetterFor('bar')->shouldBe(false);
    }
}
