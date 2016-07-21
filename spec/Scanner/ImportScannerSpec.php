<?php
namespace spec\Dkplus\Reflections\Scanner;

use Dkplus\Reflections\Scanner\ImportScanner;
use PhpSpec\ObjectBehavior;

class ImportScannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImportScanner::class);
    }

    function it_extracts_imports_from_a_file()
    {
        $this->scanForImports(__DIR__ . '/../assets/FileWithImports.php')->shouldReturn([
            'DateTimeImmutable' => 'DateTimeImmutable',
            'Wildcard' => 'Prophecy\Argument\ArgumentsWildcard',
            'DateComparator' => 'Symfony\Component\Finder\Comparator\DateComparator',
        ]);
    }
}
