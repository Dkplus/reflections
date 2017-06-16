<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\TestCase;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\DocBlock\DocBlockReflection;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function json_encode;

class DocBlockTestCase extends TestCase
{
    public static function assertSummaryEquals(string $expectedSummary, DocBlockReflection $docBlock)
    {
        self::assertEquals($expectedSummary, $docBlock->summary());
    }

    public static function assertDescriptionEquals(string $expectedDescription, DocBlockReflection $docBlock)
    {
        self::assertEquals($expectedDescription, $docBlock->description());
    }

    public static function assertAnnotationIsNotFullyQualified(string $name, DocBlockReflection $docBlock)
    {
        foreach ($docBlock->annotationsWithTag($name) as $each) {
            self::assertThat(
                $each->isFullyQualified(),
                self::isFalse(),
                "One annotation named $name is fully qualified"
            );
        }
    }

    public static function assertAnnotationIsFullyQualified(string $name, DocBlockReflection $docBlock)
    {
        foreach ($docBlock->annotationsWithTag($name) as $each) {
            self::assertThat(
                $each->isFullyQualified(),
                self::isTrue(),
                "One annotation named $name is not fully qualified"
            );
        }
    }

    public static function assertDocBlockHasAnnotationLike(AnnotationReflection $expected, DocBlockReflection $docBlock)
    {
        self::assertAnnotationsContainsOneLike($expected, $docBlock->annotations());
    }

    public static function assertAnnotationsContainsOneLike(AnnotationReflection $expected, Annotations $annotations)
    {
        $found = false;
        foreach ($annotations as $each) {
            if ($each->tag() !== $expected->tag()) {
                continue;
            }
            if ($each->isFullyQualified() !== $expected->isFullyQualified()) {
                continue;
            }
            if ($each->attributes() != $expected->attributes()) {
                continue;
            }
            $expectedInherited = $expected->inherited();
            $actualInherited = $each->inherited();
            foreach ($expectedInherited as $eachExpectedInherited) {
                try {
                    self::assertAnnotationsContainsOneLike($eachExpectedInherited, $actualInherited);
                } catch (ExpectationFailedException $exception) {
                    continue 2;
                }
            }
            $found = true;
            break;
        }
        self::assertThat($found, self::isTrue(), 'The expected annotation could not be found');
    }

    public static function assertDocBlockHasAnnotationWithNameAndAttributes(string $name, array $values, DocBlockReflection $docBlock)
    {
        self::assertThat(
            $docBlock->hasTag($name),
            self::isTrue(),
            "There is no annotation named $name"
        );
        $actualAttributes = [];
        foreach ($docBlock->annotationsWithTag($name) as $each) {
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
