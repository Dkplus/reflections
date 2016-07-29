<?php
namespace spec\Dkplus\Reflections\Type;

use Dkplus\Reflections\Type\DecoratingType;
use Dkplus\Reflections\Type\MixedType;
use Dkplus\Reflections\Type\NullableType;
use Dkplus\Reflections\Type\NullableTypeFactory;
use Dkplus\Reflections\Type\NullType;
use Dkplus\Reflections\Type\Type;
use Dkplus\Reflections\Type\TypeFactory;
use Dkplus\Reflections\Type\VoidType;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\String_;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin NullableTypeFactory
 */
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

    function it_decorates_types_if_nullable_is_passed(TypeFactory $decorated, Type $type)
    {
        $decorated->create(Argument::any(), Argument::any(), false)->willReturn($type);

        $this->create(new String_(), [], true)->shouldBeANullableVersionOf($type);
    }

    function it_decorates_types_if_no_type_is_passed_but_phpdoc_null_is_passed(TypeFactory $decorated, Type $type)
    {
        $decorated->create(Argument::any(), ['null'], false)->willReturn(new NullType());
        $decorated->create(Argument::any(), ['string'], false)->willReturn($type);

        $this->create(new Mixed(), ['null'], false)->shouldBeAnInstanceOf(NullType::class);
        $this->create(new Mixed(), ['string', 'null'], false)->shouldBeANullableVersionOf($type);
    }

    function it_does_not_decorate_mixed(TypeFactory $decorated)
    {
        $decorated->create(Argument::any(), Argument::any(), false)->willReturn(new MixedType());

        $this->create(new Mixed(), [], true)->shouldBeAnInstanceOf(MixedType::class);
    }

    function it_does_not_decorate_void(TypeFactory $decorated)
    {
        $decorated->create(Argument::any(), Argument::any(), false)->willReturn(new VoidType());

        $this->create(new Mixed(), [], true)->shouldBeAnInstanceOf(VoidType::class);
    }

    function it_does_not_decorate_null(TypeFactory $decorated)
    {
        $decorated->create(Argument::any(), Argument::any(), false)->willReturn(new NullType());

        $this->create(new Mixed(), [], true)->shouldBeAnInstanceOf(NullType::class);
    }

    function it_does_not_decorate_if_its_not_nullable(TypeFactory $decorated, Type $type)
    {
        $decorated->create(Argument::any(), Argument::any(), false)->willReturn($type);

        $this->create(new Mixed(), ['string'], false)->shouldBe($type);
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
