<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\TestCase;

use Dkplus\Reflection\DocBlock\AnnotationReflection;
use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\DocBlock\DocBlockReflection;
use PHPUnit\Framework\TestCase;
use function implode;
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

    public static function assertDocBlockHasTag(string $tag, DocBlockReflection $docBlock)
    {
        self::assertThat($docBlock->hasTag($tag), self::isTrue(), "There is no tag $tag");
    }

    public static function assertDocBlockDoesNotHaveTag(string $tag, DocBlockReflection $docBlock)
    {
        self::assertThat($docBlock->hasTag($tag), self::isFalse(), "There is a tag $tag");
    }

    public static function assertDocBlockHasUnqualifiedTag(string $tag, DocBlockReflection $docBlock)
    {
        self::assertDocBlockHasTag($tag, $docBlock);
        foreach ($docBlock->annotationsWithTag($tag) as $each) {
            self::assertThat(
                $each->isFullyQualified(),
                self::isFalse(),
                "$tag is fully qualified"
            );
        }
    }

    public static function assertDocBlockHasFullyQualifiedTag(string $tag, DocBlockReflection $docBlock)
    {
        self::assertDocBlockHasTag($tag, $docBlock);
        foreach ($docBlock->annotationsWithTag($tag) as $each) {
            self::assertThat(
                $each->isFullyQualified(),
                self::isTrue(),
                "$tag is not fully qualified"
            );
        }
    }

    public static function assertDocBlockHasAnnotationLike(AnnotationReflection $expected, DocBlockReflection $docBlock)
    {
        self::assertAnnotationsContainOneLike($expected, $docBlock->annotations());
    }

    public static function assertAnnotationsContainOneLike(AnnotationReflection $expected, Annotations $annotations)
    {
        $found = false;
        foreach ($annotations as $each) {
            if (self::annotationMatches($expected, $each)) {
                $found = true;
                break;
            }
        }
        self::assertThat(
            $found,
            self::isTrue(),
            "The expected annotation \n $expected \n could not be found in \n"
            . implode("\n", $annotations->withTag($expected->tag())->map('strval'))
        );
    }

    private static function annotationMatches(AnnotationReflection $expected, AnnotationReflection $actual)
    {
        if ($expected->tag() !== $actual->tag()) {
            return false;
        }
        $actualAttributes = $actual->attributes();
        foreach ($expected->attributes() as $eachExpectedKeys => $eachExpectedAttribute) {
            if (! isset($actualAttributes[$eachExpectedKeys])) {
                return false;
            }
            if ($eachExpectedAttribute instanceof AnnotationReflection) {
                if (! $actualAttributes[$eachExpectedKeys] instanceof AnnotationReflection) {
                    return false;
                }
                if (! self::annotationMatches($eachExpectedAttribute, $actualAttributes[$eachExpectedKeys])) {
                    return false;
                }
                continue;
            }
            if ($eachExpectedAttribute !== $actualAttributes[$eachExpectedKeys]) {
                return false;
            }
        }
        foreach ($expected->attached() as $eachInherited) {
            foreach ($actual->attached() as $eachActual) {
                if (self::annotationMatches($eachInherited, $eachActual)) {
                    continue 2;
                }
            }
            return false;
        }
        return true;
    }

    public static function assertDocBlockHasAnnotationWithTagAndAttributes(
        string $tag,
        array $attributes,
        DocBlockReflection $docBlock
    ) {
        self::assertDocBlockHasTag($tag, $docBlock);
        $actualAttributes = [];
        foreach ($docBlock->annotationsWithTag($tag) as $each) {
            $actualAttributes[] = $each->attributes();
        }
        self::assertThat(
            $actualAttributes,
            self::contains($attributes, false, false),
            "There are annotations with tag $tag but they have other attributes "
            . '(found: ' . json_encode($actualAttributes) . ')'
        );
    }
}
