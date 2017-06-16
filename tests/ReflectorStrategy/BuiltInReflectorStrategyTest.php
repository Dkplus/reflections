<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\ReflectorStrategy;

use ArrayIterator;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\ReflectorStrategy\BuiltInReflectorStrategy;
use Dkplus\Reflection\Type\ArrayType;
use Dkplus\Reflection\Type\ClassType;
use Dkplus\Reflection\Type\IterableType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\NullableType;
use Dkplus\Reflection\Type\StringType;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use stdClass;
use test\Dkplus\Reflection\DocBlock\Fixtures\PhpDocAnnotations;
use test\Dkplus\Reflection\Fixtures\AbstractClass;
use test\Dkplus\Reflection\Fixtures\AnotherInterface;
use test\Dkplus\Reflection\Fixtures\AnotherTrait;
use test\Dkplus\Reflection\Fixtures\ClassWithExplicitPackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithExplicitSubpackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithInterface;
use test\Dkplus\Reflection\Fixtures\ClassWithMethods;
use test\Dkplus\Reflection\Fixtures\ClassWithoutExplicitPackageDeclaration;
use test\Dkplus\Reflection\Fixtures\ClassWithParent;
use test\Dkplus\Reflection\Fixtures\ClassWithProperties;
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

    /** @test */
    function it_reflects_the_annotations_of_a_class()
    {
        self::assertDocBlockHasAnnotationWithNameAndAttributes(
            'internal',
            ['description' => ''],
            $this->underTest->reflectClass(PhpDocAnnotations::class)->docBlock()
        );
    }

    /** @test */
    function it_reflects_the_methods_of_a_class()
    {
        self::assertClassHasMethod(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'oneMethod'
        );
    }

    /** @test */
    function it_knows_whether_a_method_is_final()
    {
        self::assertMethodIsFinal(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'finalMethod'
        );
        self::assertMethodIsNotFinal(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'oneMethod'
        );
    }

    /** @test */
    function it_knows_the_visibility_of_a_method()
    {
        self::assertMethodIsPublic($this->underTest->reflectClass(ClassWithMethods::class), 'publicMethod');
        self::assertMethodIsProtected($this->underTest->reflectClass(ClassWithMethods::class), 'protectedMethod');
        self::assertMethodIsPrivate($this->underTest->reflectClass(ClassWithMethods::class), 'privateMethod');
    }

    /** @test */
    function it_knows_whether_a_method_is_abstract()
    {
        self::assertMethodIsAbstract($this->underTest->reflectClass(ClassWithMethods::class), 'abstractMethod');
        self::assertMethodIsNotAbstract($this->underTest->reflectClass(ClassWithMethods::class), 'oneMethod');
    }

    /** @test */
    function it_knows_whether_a_method_is_static()
    {
        self::assertMethodIsStatic($this->underTest->reflectClass(ClassWithMethods::class), 'staticMethod');
        self::assertMethodIsNotStatic($this->underTest->reflectClass(ClassWithMethods::class), 'oneMethod');
    }

    /** @test */
    function it_uses_mixed_as_return_type_if_no_return_type_is_available()
    {
        self::assertReturnTypeIs($this->underTest->reflectClass(ClassWithMethods::class), 'noReturnType',
            new MixedType());
    }

    /** @test */
    function it_uses_the_return_type_hint_as_return_type()
    {
        self::assertReturnTypeIs($this->underTest->reflectClass(ClassWithMethods::class), 'stringReturnType',
            new StringType());
    }

    /** @test */
    function it_uses_the_return_tag_as_return_type()
    {
        self::assertReturnTypeIs($this->underTest->reflectClass(ClassWithMethods::class), 'stringReturnTag',
            new StringType());
    }

    /** @test */
    function it_supports_class_return_types()
    {
        self::assertReturnTypeIs(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'returnsObject',
            new ClassType(new ReflectionClass(OneClass::class))
        );
    }

    /** @test */
    public function it_knows_method_parameters()
    {
        self::assertMethodHasParameter(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithParameters',
            'value'
        );
    }

    /** @test */
    function it_knows_the_position_of_parameters()
    {
        self::assertMethodParameterHasPosition(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithParameters',
            'value',
            0
        );
    }

    /** @test */
    function it_considers_the_type_hint_for_parameter_types()
    {
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithTypeHintParameter',
            'stringParam',
            new StringType()
        );
    }

    /** @test */
    function it_considers_the_doc_type_for_parameter_types()
    {
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithDocTypeParameter',
            'stringParam',
            new StringType()
        );
    }

    /** @test */
    function it_combines_the_type_hint_and_the_doc_types_for_parameters_if_both_are_available()
    {
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithDocTypeAndTypeHintParameter',
            'stringArrayParam',
            new ArrayType(new StringType())
        );
    }

    /** @test */
    function it_appends_parameters_from_phpdoc_if_they_are_not_available_as_real_parameters()
    {
        self::assertMethodParameterHasPosition(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithAdditionalDocBlockTags',
            'docBlock',
            1
        );
    }

    /** @test */
    function it_adds_the_description_from_phpdoc_if_available()
    {
        self::assertMethodParameterDescriptionEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithParameterDescription',
            'parameter',
            'Parameter description'
        );
    }

    /** @test */
    public function it_supports_omittable_parameters()
    {
        self::assertMethodParameterCanBeOmitted(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithOmittableParameter',
            'omittable'
        );
        self::assertMethodParameterCannotBeOmitted(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithOmittableParameter',
            'nonOmittable'
        );
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithOmittableParameter',
            'omittable',
            new NullableType(new StringType())
        );
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithOmittableParameter',
            'omittableWithStringDefault',
            new StringType()
        );
    }

    /** @test */
    public function it_supports_variadic_parameters()
    {
        self::assertMethodParameterIsNotVariadic(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithVariadic',
            'nonVariadic'
        );
        self::assertMethodParameterIsVariadic(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithVariadic',
            'variadic'
        );
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithVariadic',
            'variadic',
            new IterableType(new StringType())
        );
    }

    /** @test */
    public function it_supports_variadic_parameters_with_phpdoc()
    {
        self::assertMethodParameterTypeEquals(
            $this->underTest->reflectClass(ClassWithMethods::class),
            'methodWithVariadicAndDocBlock',
            'variadic',
            new IterableType(new StringType())
        );
    }

    /** @test */
    function it_parses_the_method_annotations()
    {
        self::assertDocBlockHasAnnotationWithNameAndAttributes(
            'return',
            ['type' => new String_(), 'description' => ''],
            $this->underTest
                ->reflectClass(ClassWithMethods::class)
                ->methods()
                ->named('stringReturnTag')
                ->docBlock()
        );
    }

    /** @test */
    function it_knows_all_properties_of_a_class()
    {
        self::assertPropertyExists(
            $this->underTest->reflectClass(ClassWithProperties::class),
            'publicProperty'
        );
    }

    /** @test */
    function it_knows_the_type_of_the_properties()
    {
        self::assertPropertyTypeEquals(
            $this->underTest->reflectClass(ClassWithProperties::class),
            'propertyWithType',
            new StringType()
        );
    }

    /** @test */
    function it_knows_the_visibility_of_the_properties()
    {
        $reflection = $this->underTest->reflectClass(ClassWithProperties::class);
        self::assertPropertyIsPublic($reflection, 'publicProperty');
        self::assertPropertyIsProtected($reflection, 'protectedProperty');
        self::assertPropertyIsPrivate($reflection, 'privateProperty');
    }

    /** @test */
    function it_knows_whether_a_property_is_static()
    {
        self::assertPropertyIsStatic(
            $this->underTest->reflectClass(ClassWithProperties::class),
            'staticProperty'
        );
        self::assertPropertyIsNotStatic(
            $this->underTest->reflectClass(ClassWithProperties::class),
            'publicProperty'
        );
    }

    /** @test */
    function it_knows_the_annotations_of_the_properties()
    {
        self::assertDocBlockHasAnnotationWithNameAndAttributes(
            'var',
            ['type' => new String_(), 'description' => ''],
            $this->underTest
                ->reflectClass(ClassWithProperties::class)
                ->properties()
                ->named('propertyWithType')
                ->docBlock()
        );
    }
}
