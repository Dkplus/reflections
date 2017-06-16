<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
use Dkplus\Reflection\DocBlock\DocBlockReflection;
use Dkplus\Reflection\DocBlock\DocBlockReflector;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionProperty;
use test\Dkplus\Reflection\DocBlock\Fixtures\DocBlockBasics;
use test\Dkplus\Reflection\DocBlock\TestCase\DocBlockTestCase;

/**
 * @covers AnnotationFactory
 * @covers DocBlockVisitor
 * @covers HoaParser
 */
class DocBlockBasicsTest extends DocBlockTestCase
{
    /** @var DocBlockReflector */
    private $reflector;

    protected function setUp()
    {
        $this->reflector = new DocBlockReflector(new BuiltInClassReflector(), new FqsenResolver());
    }

    /** @test */
    function it_assumes_empty_summary_and_description_on_missing_doc_blocks()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'noDocBlock');
        self::assertSummaryEquals('', $docBlock);
        self::assertDescriptionEquals('', $docBlock);
    }

    /** @test */
    function it_parses_the_summary_from_single_line_doc_blocks()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'singleLineSummary');
        self::assertSummaryEquals('This is the summary', $docBlock);
        self::assertDescriptionEquals('', $docBlock);
    }

    /** @test */
    function it_parses_summary_and_description_if_both_exist()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'summaryAndDescription');
        self::assertSummaryEquals('This is the summary.', $docBlock);
        self::assertDescriptionEquals('This is the description. This is also the description.', $docBlock);
    }

    /** @test */
    function it_detects_the_right_ending_of_a_summary_also_if_its_ending_with_an_empty_line()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'singleLineSummaryWithoutFullStop');
        self::assertSummaryEquals('This is the summary', $docBlock);
        self::assertDescriptionEquals('This is the description. This is also the description', $docBlock);
    }

    /** @test */
    function it_detects_multi_line_summaries()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'multiLineSummary');
        self::assertSummaryEquals('This is the summary and this is also the summary', $docBlock);
        self::assertDescriptionEquals('', $docBlock);
    }

    /** @test */
    function it_detects_multi_line_summaries_without_full_stop_and_with_following_description()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'multiLineSummaryWithoutFullStopAndWithFollowingDescription');
        self::assertSummaryEquals('This is the summary and this is also the summary', $docBlock);
        self::assertDescriptionEquals('This is the description. This is also the description', $docBlock);
    }

    /** @test */
    function it_retains_the_newlines_of_the_description()
    {
        $docBlock = $this->reflectDocBlockOfProperty(DocBlockBasics::class, 'descriptionWithMultipleParagraphs');
        self::assertSummaryEquals('summary', $docBlock);
        self::assertDescriptionEquals(
            "first paragraph\nsecond paragraph and still second paragraph\nthird paragraph",
            $docBlock
        );
    }

    private function reflectDocBlockOfProperty(string $class, string $property): DocBlockReflection
    {
        $reflector = new ReflectionProperty($class, $property);
        return $this->reflector->reflectDocBlock(
            (string) $reflector->getDocComment(),
            (new ContextFactory())->createFromReflector($reflector)
        );
    }
}
