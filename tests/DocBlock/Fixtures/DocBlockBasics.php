<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\Fixtures;

class DocBlockBasics
{
    public $noDocBlock;

    /** This is the summary */
    public $singleLineSummary;

    /**
     * This is the summary.
     * This is the description.
     * This is also the description.
     */
    public $summaryAndDescription;

    /**
     * This is the summary
     *
     * This is the description.
     * This is also the description
     */
    public $singleLineSummaryWithoutFullStop;

    /**
     * This is the summary
     * and this is also the summary
     */
    public $multiLineSummary;

    /**
     * This is the summary
     * and this is also the summary
     *
     * This is the description.
     * This is also the description
     */
    public $multiLineSummaryWithoutFullStopAndWithFollowingDescription;

    /**
     * summary
     *
     * first paragraph
     *
     * second paragraph
     * and still second paragraph
     *
     * third paragraph
     */
    public $descriptionWithMultipleParagraphs;
}
