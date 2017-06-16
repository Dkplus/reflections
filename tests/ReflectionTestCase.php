<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection;

use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use Dkplus\Reflection\Type\Type;
use test\Dkplus\Reflection\DocBlock\DocBlockTestCase;

class ReflectionTestCase extends DocBlockTestCase
{
    public static function assertPackageIs(string $expected, ClassReflection $class)
    {
        self::assertEquals($expected, $class->packageName());
    }

    public static function assertClassIsFinal(ClassReflection $class)
    {
        self::assertThat($class->isFinal(), self::isTrue(), 'Class ' . $class->name() . ' is not final');
    }

    public static function assertClassIsNotFinal(ClassReflection $class)
    {
        self::assertThat($class->isFinal(), self::isFalse(), 'Class ' . $class->name() . ' is final');
    }

    public static function assertClassIsAbstract(ClassReflection $class)
    {
        self::assertThat($class->isAbstract(), self::isTrue(), 'Class ' . $class->name() . ' is not abstract');
    }

    public static function assertClassIsNotAbstract(ClassReflection $class)
    {
        self::assertThat($class->isAbstract(), self::isFalse(), 'Class ' . $class->name() . ' is abstract');
    }

    public static function assertClassIsInterface(ClassReflection $class)
    {
        self::assertThat($class->isInterface(), self::isTrue(), 'Class ' . $class->name() . ' is no interface');
    }

    public static function assertClassIsNoInterface(ClassReflection $class)
    {
        self::assertThat($class->isInterface(), self::isFalse(), 'Class ' . $class->name() . ' is an interface');
    }

    public static function assertClassIsTrait(ClassReflection $class)
    {
        self::assertThat($class->isTrait(), self::isTrue(), 'Class ' . $class->name() . ' is no trait');
    }

    public static function assertClassIsNoTrait(ClassReflection $class)
    {
        self::assertThat($class->isTrait(), self::isFalse(), 'Class ' . $class->name() . ' is a trait');
    }

    public static function assertClassIsInternal(ClassReflection $class)
    {
        self::assertThat($class->isInternal(), self::isTrue(), 'Class ' . $class->name() . ' is not internals');
    }

    public static function assertClassIsNotInternal(ClassReflection $class)
    {
        self::assertThat($class->isInternal(), self::isFalse(), 'Class ' . $class->name() . ' is internal');
    }

    public static function assertClassIsIterateable(ClassReflection $class)
    {
        self::assertThat($class->isIterateable(), self::isTrue(), 'Class ' . $class->name() . ' is not iterateable');
    }

    public static function assertClassIsNotIterateable(ClassReflection $class)
    {
        self::assertThat($class->isIterateable(), self::isFalse(), 'Class ' . $class->name() . ' is iterateable');
    }

    public static function assertClassesHaveNames(array $parents, Classes $classes)
    {
        self::assertEquals($parents, $classes->map(function (ClassReflection $class) {
            return $class->name();
        }));
    }

    public static function assertClassHasMethod(ClassReflection $class, string $methodName)
    {
        self::assertThat(
            $class->methods()->contains($methodName),
            self::isTrue(),
            'Class ' . $class->name() . ' does not contain a method ' . $methodName
        );
    }

    public static function assertMethodIsFinal(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isFinal(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not final"
        );
    }

    public static function assertMethodIsNotFinal(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isFinal(),
            self::isFalse(),
            'Method ' . $class->name() . "::$methodName is final"
        );
    }

    public static function assertMethodIsPublic(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isPublic(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not public"
        );
    }

    public static function assertMethodIsProtected(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isProtected(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not protected"
        );
    }

    public static function assertMethodIsPrivate(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isPrivate(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not private"
        );
    }

    public static function assertMethodIsAbstract(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isAbstract(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not abstract"
        );
    }

    public static function assertMethodIsNotAbstract(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isAbstract(),
            self::isFalse(),
            'Method ' . $class->name() . "::$methodName is abstract"
        );
    }

    public static function assertMethodIsStatic(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isStatic(),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName is not static"
        );
    }

    public static function assertMethodIsNotStatic(ClassReflection $class, string $methodName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->isStatic(),
            self::isFalse(),
            'Method ' . $class->name() . "::$methodName is static"
        );
    }

    public static function assertReturnTypeIs(ClassReflection $class, string $methodName, Type $type)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertEquals($type, $class->methods()->named($methodName)->returnType());
    }


    public static function assertMethodHasParameter(ClassReflection $class, string $methodName, string $parameterName)
    {
        self::assertClassHasMethod($class, $methodName);
        self::assertThat(
            $class->methods()->named($methodName)->parameters()->contains($parameterName),
            self::isTrue(),
            'Method ' . $class->name() . "::$methodName() has no parameter $parameterName"
        );
    }

    public static function assertMethodParameterHasPosition(
        ClassReflection $class,
        string $methodName,
        string $parameterName,
        int $expectedPosition
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertEquals(
            $expectedPosition,
            $parameter->position(),
            sprintf('Expected position %d but got position %d', $expectedPosition, $parameter->position())
        );
    }


    public static function assertMethodParameterDescriptionEquals(
        ClassReflection $class,
        string $methodName,
        string $parameterName,
        string $expectedDescription
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertEquals(
            $expectedDescription,
            $parameter->description()
        );
    }

    public static function assertMethodParameterTypeEquals(
        ClassReflection $class,
        string $methodName,
        string $parameterName,
        Type $expectedType
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertEquals($expectedType, $parameter->type());
    }

    public static function assertMethodParameterCanBeOmitted(
        ClassReflection $class,
        string $methodName,
        string $parameterName
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertThat(
            $parameter->canBeOmitted(),
            self::isTrue(),
            "Parameter $parameterName cannot be omitted"
        );
    }

    public static function assertMethodParameterCannotBeOmitted(
        ClassReflection $class,
        string $methodName,
        string $parameterName
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertThat(
            $parameter->canBeOmitted(),
            self::isFalse(),
            "Parameter $parameterName can be omitted"
        );
    }

    public static function assertMethodParameterIsVariadic(
        ClassReflection $class,
        string $methodName,
        string $parameterName
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertThat(
            $parameter->isVariadic(),
            self::isTrue(),
            "Parameter $parameterName is not variadic"
        );
    }

    public static function assertMethodParameterIsNotVariadic(
        ClassReflection $class,
        string $methodName,
        string $parameterName
    ) {
        self::assertClassHasMethod($class, $methodName);
        self::assertMethodHasParameter($class, $methodName, $parameterName);
        $parameter = $class->methods()->named($methodName)->parameters()->named($parameterName);
        self::assertThat(
            $parameter->isVariadic(),
            self::isFalse(),
            "Parameter $parameterName is variadic"
        );
    }

    public static function assertPropertyExists(ClassReflection $class, string $property)
    {
        self::assertThat(
            $class->properties()->contains($property),
            self::isTrue(),
            sprintf('Expected %s to contain a property %s but it does not', $class->name(), $property)
        );
    }

    public static function assertPropertyTypeEquals(ClassReflection $class, string $property, Type $expectedType)
    {
        self::assertPropertyExists($class, $property);
        self::assertEquals($expectedType, $class->properties()->named($property)->type());
    }

    public static function assertPropertyIsPublic(ClassReflection $class, string $property)
    {
        self::assertPropertyExists($class, $property);
        self::assertThat(
            $class->properties()->named($property)->isPublic(),
            self::isTrue(),
            sprintf('Expected %s::%s to be public but it is not', $class->name(), $property)
        );
    }

    public static function assertPropertyIsProtected(ClassReflection $class, string $property)
    {
        self::assertPropertyExists($class, $property);
        self::assertThat(
            $class->properties()->named($property)->isProtected(),
            self::isTrue(),
            sprintf('Expected %s::%s to be protected but it is not', $class->name(), $property)
        );
    }

    public static function assertPropertyIsPrivate(ClassReflection $class, string $property)
    {
        self::assertPropertyExists($class, $property);
        self::assertThat(
            $class->properties()->named($property)->isPrivate(),
            self::isTrue(),
            sprintf('Expected %s::%s to be private but it is not', $class->name(), $property)
        );
    }

    public static function assertPropertyIsStatic(ClassReflection $class, string $property)
    {
        self::assertPropertyExists($class, $property);
        self::assertThat(
            $class->properties()->named($property)->isStatic(),
            self::isTrue(),
            sprintf('Expected %s::%s to be static but it is not', $class->name(), $property)
        );
    }
    
    public static function assertPropertyIsNotStatic(ClassReflection $class, string $property)
    {
        self::assertPropertyExists($class, $property);
        self::assertThat(
            $class->properties()->named($property)->isStatic(),
            self::isFalse(),
            sprintf('Expected %s::%s not to be static but it is', $class->name(), $property)
        );
    }
}
