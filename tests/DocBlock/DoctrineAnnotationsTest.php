<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
use Dkplus\Reflection\DocBlock\DocBlockReflector;
use Doctrine\Common\Annotations\Annotation;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
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
        $reflector = new ReflectionClass($class);
        $docBlock = $this->reflector->reflectDocBlock(
            (string) $reflector->getDocComment(),
            (new ContextFactory())->createFromReflector($reflector)
        );
        self::assertDocBlockHasAnnotationLike($expectedAnnotation, $docBlock);
    }

    public static function provideExpectedAnnotations()
    {
        return [
            [ClassWithAnnotations::class, AnnotationReflection::fullyQualified(
                '\\' . OneAnnotation::class,
                []
            )],
            [ClassWithAnnotations::class, AnnotationReflection::fullyQualified(
                '\\' . OneAnnotation::class,
                [],
                AnnotationReflection::fullyQualified('\\' . Annotation::class, []),
                AnnotationReflection::fullyQualified('\\' . Annotation\Target::class, ['CLASS'])
            )],
        ];
    }
}
