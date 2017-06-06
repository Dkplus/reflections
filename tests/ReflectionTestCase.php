<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection;

use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\Classes;
use Dkplus\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use function json_encode;

class ReflectionTestCase extends TestCase
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

    public static function assertAnnotationIsNotFullyQualified(string $name, Annotations $annotations)
    {
        foreach ($annotations->named($name) as $each) {
            self::assertThat(
                $each->isFullyQualified(),
                self::isFalse(),
                "One annotation named $name is fully qualified"
            );
        }
    }

    public static function assertAnnotationExistsWithAttributes(string $name, array $values, Annotations $annotations)
    {
        $named = $annotations->named($name);
        self::assertThat(
            count($named),
            self::greaterThanOrEqual(1),
            "There is no annotation named $name"
        );
        $actualAttributes = [];
        foreach ($named as $each) {
            $actualAttributes[] = $each->attributes();
        }
        self::assertThat(
            $actualAttributes,
            self::contains($values, false, false),
            "There are annotations named $name but they have other attributes "
            . '(found: ' . json_encode($actualAttributes) . ')'
        );
    }
}
