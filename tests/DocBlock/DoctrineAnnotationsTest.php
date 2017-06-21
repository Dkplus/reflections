<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
use Dkplus\Reflection\DocBlock\DocBlockReflector;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use phpDocumentor\Reflection\FqsenResolver;
use ReflectionClass;
use test\Dkplus\Reflection\DocBlock\Fixtures\AnotherAnnotation;
use test\Dkplus\Reflection\DocBlock\Fixtures\ClassWithAnnotations;
use test\Dkplus\Reflection\DocBlock\Fixtures\OneAnnotation;
use test\Dkplus\Reflection\DocBlock\TestCase\DocBlockTestCase;

/**
 * @covers AnnotationFactory
 * @covers DocBlockVisitor
 * @covers HoaParser
 */
class DoctrineAnnotationsTest extends DocBlockTestCase
{
    /** @var DocBlockReflector */
    private $reflector;

    protected function setUp()
    {
        $this->reflector = new DocBlockReflector(new BuiltInClassReflector(), new FqsenResolver());
    }

    /**
     * @test
     * @dataProvider provideExpectedAnnotations
     */
    public function it_parses_doctrine_annotations(string $class, AnnotationReflection $expectedAnnotation)
    {
        $docBlock = $this->reflector->reflectDocBlockOf(new ReflectionClass($class));
        self::assertDocBlockHasAnnotationLike($expectedAnnotation, $docBlock);
    }

    public static function provideExpectedAnnotations()
    {
        return [
            '@OneAnnotation' => [
                ClassWithAnnotations::class,
                AnnotationReflection::fullyQualified(
                    OneAnnotation::class,
                    []
                ),
            ],
            '@OneAnnotation with inherited' => [
                ClassWithAnnotations::class,
                AnnotationReflection::fullyQualified(
                    OneAnnotation::class,
                    [],
                    AnnotationReflection::fullyQualified(Annotation::class, []),
                    AnnotationReflection::fullyQualified(Target::class, ['CLASS'])
                ),
            ],
            '@AnotherAnnotation()' => [
                ClassWithAnnotations::class,
                AnnotationReflection::fullyQualified(
                    AnotherAnnotation::class,
                    [],
                    AnnotationReflection::fullyQualified(Annotation::class, []),
                    AnnotationReflection::fullyQualified(Target::class, [Target::TARGET_CLASS])
                ),
            ],
            '@AnotherAnnotation({"foo": @OneAnnotation("bar")})' => [
                ClassWithAnnotations::class,
                AnnotationReflection::fullyQualified(
                    AnotherAnnotation::class,
                    ['foo' => AnnotationReflection::fullyQualified(OneAnnotation::class, ['bar'])]
                ),
            ],
            '@AnotherAnnotation({"bar" = @OneAnnotation({"foo","bar"})})' => [
                ClassWithAnnotations::class,
                AnnotationReflection::fullyQualified(
                    AnotherAnnotation::class,
                    ['bar' => AnnotationReflection::fullyQualified(OneAnnotation::class, ['foo', 'bar'])]
                ),
            ],
        ];
    }
}
