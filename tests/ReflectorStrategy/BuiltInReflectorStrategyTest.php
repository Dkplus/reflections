<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\ReflectorStrategy;

use ArrayIterator;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\ReflectorStrategy\BuiltInReflectorStrategy;
use stdClass;
use test\Dkplus\Reflection\Fixtures\AbstractClass;
use test\Dkplus\Reflection\Fixtures\AnotherInterface;
use test\Dkplus\Reflection\Fixtures\AnotherTrait;
use test\Dkplus\Reflection\Fixtures\ClassWithExplicitPackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithExplicitSubpackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithInterface;
use test\Dkplus\Reflection\Fixtures\ClassWithoutExplicitPackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithParent;
use test\Dkplus\Reflection\Fixtures\ClassWithTrait;
use test\Dkplus\Reflection\Fixtures\ClassWithTwoParents;
use test\Dkplus\Reflection\Fixtures\FinalClass;
use test\Dkplus\Reflection\Fixtures\InterfaceWithParent;
use test\Dkplus\Reflection\Fixtures\InterfaceWithTwoParents;
use test\Dkplus\Reflection\Fixtures\OneClass;
use test\Dkplus\Reflection\Fixtures\OneInterface;
use test\Dkplus\Reflection\Fixtures\OneTrait;
use test\Dkplus\Reflection\Fixtures\TraitUsesTrait;
use test\Dkplus\Reflection\ReflectionTestCase;

/**
 * @covers BuiltInReflectorStrategy
 * @covers ClassReflection
 */
class BuiltInReflectorStrategyTest extends ReflectionTestCase
{
    /** @var BuiltInReflectorStrategy */
    private $underTest;

    protected function setUp()
    {
        $this->underTest = new BuiltInReflectorStrategy();
    }

    /** @test */
    function it_reflects_classes()
    {
        self::assertInstanceOf(ClassReflection::class, $this->underTest->reflectClass(OneClass::class));
        self::assertEquals(OneClass::class, $this->underTest->reflectClass(OneClass::class)->name());
        self::assertEquals('OneClass', $this->underTest->reflectClass(OneClass::class)->shortName());
        self::assertEquals(
            'test\Dkplus\Reflection\Fixtures',
            $this->underTest->reflectClass(OneClass::class)->namespace()
        );
    }

    /** @test */
    public function it_uses_the_namespace_as_package_if_no_package_annotation_is_available()
    {
        self::assertPackageIs(
            'test\Dkplus\Reflection\Fixtures',
            $this->underTest->reflectClass(ClassWithoutExplicitPackageDeclaration::class)
        );
    }

    /** @test */
    function it_uses_a_package_annotation_if_such_exist()
    {
        self::assertPackageIs(
            'Fixtures',
            $this->underTest->reflectClass(ClassWithExplicitPackageDeclaration::class)
        );
    }

    /** @test */
    function it_also_uses_a_subpackage_annotation_if_such_exist()
    {
        self::assertPackageIs(
            'Fixtures\\Subpackage',
            $this->underTest->reflectClass(ClassWithExplicitSubpackageDeclaration::class)
        );
    }

    /** @test */
    function it_detects_whether_a_class_is_final()
    {
        self::assertClassIsFinal($this->underTest->reflectClass(FinalClass::class));
        self::assertClassIsNotFinal($this->underTest->reflectClass(OneClass::class));
    }

    /** @test */
    function it_detects_whether_a_class_is_abstract()
    {
        self::assertClassIsAbstract($this->underTest->reflectClass(AbstractClass::class));
        self::assertClassIsNotAbstract($this->underTest->reflectClass(OneClass::class));
    }

    /** @test */
    function it_assumes_interfaces_are_abstract()
    {
        self::assertClassIsAbstract($this->underTest->reflectClass(OneInterface::class));
    }

    /** @trait */
    function it_knows_whether_a_class_is_an_interface_or_not()
    {
        self::assertClassIsInterface($this->underTest->reflectClass(OneInterface::class));
        self::assertClassIsNoInterface($this->underTest->reflectClass(OneClass::class));
    }

    /** @trait */
    function it_knows_whether_a_class_is_a_trait_or_not()
    {
        self::assertClassIsTrait($this->underTest->reflectClass(OneTrait::class));
        self::assertClassIsNoTrait($this->underTest->reflectClass(OneClass::class));
    }

    /** @test */
    function it_detects_whether_a_class_is_internal_or_not()
    {
        self::assertClassIsInternal($this->underTest->reflectClass(stdClass::class));
        self::assertClassIsNotInternal($this->underTest->reflectClass(OneInterface::class));
    }

    /** @test */
    function it_detects_whether_a_class_is_iterateable_or_not()
    {
        self::assertClassIsIterateable($this->underTest->reflectClass(ArrayIterator::class));
        self::assertClassIsNotIterateable($this->underTest->reflectClass(stdClass::class));
    }

    /** @test */
    function it_detects_the_immediate_parent_classes_of_a_class()
    {
        self::assertClassesHaveNames(
            [ClassWithInterface::class],
            $this->underTest->reflectClass(ClassWithParent::class)->immediateParentClasses()
        );
    }

    /** @test */
    function it_detects_all_parent_classes_of_a_class()
    {
        self::assertClassesHaveNames(
            [ClassWithParent::class, ClassWithInterface::class],
            $this->underTest->reflectClass(ClassWithTwoParents::class)->parentClasses()
        );
    }

    /** @test */
    function it_detects_the_immediate_parent_classes_of_an_interface()
    {
        self::assertClassesHaveNames(
            [InterfaceWithParent::class, AnotherInterface::class],
            $this->underTest->reflectClass(InterfaceWithTwoParents::class)->immediateParentClasses()
        );
    }

    /** @test */
    function it_detects_all_parent_classes_of_an_interface()
    {
        self::assertClassesHaveNames(
            [InterfaceWithParent::class, AnotherInterface::class, OneInterface::class],
            $this->underTest->reflectClass(InterfaceWithTwoParents::class)->parentClasses()
        );
    }

    /** @test */
    function it_detects_whether_a_class_is_a_parent_of_another_class()
    {
        self::assertTrue($this->underTest->reflectClass(ClassWithParent::class)->extendsClass(ClassWithInterface::class));
        self::assertFalse($this->underTest->reflectClass(ClassWithParent::class)->extendsClass(OneClass::class));
    }

    /** @test */
    function it_detects_the_immediate_implemented_interfaces_of_a_class()
    {
        self::assertClassesHaveNames(
            [InterfaceWithParent::class],
            $this->underTest->reflectClass(ClassWithInterface::class)->immediateImplementedInterfaces()
        );
    }

    /** @test */
    function it_detects_all_implemented_interfaces_of_a_class()
    {
        self::assertClassesHaveNames(
            [AnotherInterface::class, InterfaceWithParent::class, OneInterface::class],
            $this->underTest->reflectClass(ClassWithParent::class)->implementedInterfaces()
        );
    }

    /** @test */
    function it_detects_the_immediate_used_traits_of_a_class()
    {
        self::assertClassesHaveNames(
            [TraitUsesTrait::class],
            $this->underTest->reflectClass(ClassWithTrait::class)->immediateUsedTraits()
        );
        self::assertClassesHaveNames(
            [OneTrait::class],
            $this->underTest->reflectClass(TraitUsesTrait::class)->immediateUsedTraits()
        );
    }

    /** @test */
    function it_detects_all_used_traits_of_a_class()
    {
        self::assertClassesHaveNames(
            [TraitUsesTrait::class, AnotherTrait::class, OneTrait::class],
            $this->underTest->reflectClass(ClassWithTrait::class)->usedTraits()
        );
    }
}
