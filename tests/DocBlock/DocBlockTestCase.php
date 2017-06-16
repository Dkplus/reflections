<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\DocBlockReflection;
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

    public static function assertAnnotationExistsWithAttributes(string $name, array $values, DocBlockReflection $docBlock)
    {
        $named = $docBlock->annotationsWithTag($name);
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
