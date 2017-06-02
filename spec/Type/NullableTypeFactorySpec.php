<?php
declare(strict_types=1);

namespace spec\Dkplus\Reflection\Type;

use Dkplus\Reflection\ReflectorStrategy;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\NullableTypeFactory;
use Dkplus\Reflection\Type\NullType;
use Dkplus\Reflection\Type\Type;
use Dkplus\Reflection\Type\TypeFactory;
use Dkplus\Reflection\Type\VoidType;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NullableTypeFactorySpec extends ObjectBehavior
{
    function let(TypeFactory $decorated)
    {
        $this->beConstructedWith($decorated);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NullableTypeFactory::class);
    }

    function it_is_a_type_factory()
    {
        $this->shouldImplement(TypeFactory::class);
    }

    function it_decorates_types_if_nullable_is_passed(ReflectorStrategy $reflector, TypeFactory $decorated, Type $type)
    {
        $decorated->create($reflector, Argument::any(), Argument::any(), false)->willReturn($type);

        $this->create($reflector, new String_(), [], true)->shouldBeANullableVersionOf($type);
    }

    function it_decorates_types_if_no_type_is_passed_but_phpdoc_null_is_passed(
        ReflectorStrategy $reflector,
        TypeFactory $decorated,
        Type $type
    ) {
        $decorated->create($reflector, Argument::any(), ['null'], false)->willReturn(new NullType());
        $decorated->create($reflector, Argument::any(), ['string'], false)->willReturn($type);

        $this->create($reflector, new Mixed(), ['null'], false)->shouldBeAnInstanceOf(NullType::class);
        $this->create($reflector, new Mixed(), ['string', 'null'], false)->shouldBeANullableVersionOf($type);
    }

    function it_does_not_decorate_mixed(ReflectorStrategy $reflector, TypeFactory $decorated)
    {
        $decorated->create($reflector, Argument::any(), Argument::any(), false)->willReturn(new MixedType());

        $this->create($reflector, new Mixed(), [], true)->shouldBeAnInstanceOf(MixedType::class);
    }

    function it_does_not_decorate_void(ReflectorStrategy $reflector, TypeFactory $decorated)
    {
        $decorated->create($reflector, Argument::any(), Argument::any(), false)->willReturn(new VoidType());

        $this->create($reflector, new Mixed(), [], true)->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_does_not_decorate_null(ReflectorStrategy $reflector, TypeFactory $decorated)
    {
        $decorated->create($reflector, Argument::any(), Argument::any(), false)->willReturn(new NullType());

        $this->create($reflector, new Mixed(), [], true)->shouldBeAnInstanceOf(NullType::class);
    }

    function it_does_not_decorate_if_its_not_nullable(ReflectorStrategy $reflector, TypeFactory $decorated, Type $type)
    {
        $decorated->create($reflector, Argument::any(), Argument::any(), false)->willReturn($type);

        $this->create($reflector, new Mixed(), ['string'], false)->shouldBe($type);
    }

    public function getMatchers()
    {
        return [
            'beANullableVersionOf' => function (Type $subject, Type $type) {
                return $subject instanceof NullableType
                    && $subject->decoratedType() === $type;
            },
        ];
    }
}
