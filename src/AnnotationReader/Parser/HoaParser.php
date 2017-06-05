<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Hoa\Compiler\Llk\Llk;
use Hoa\File\Read;
use Hoa\Visitor\Visit;

final class HoaParser
{
    /**
     * @var \Hoa\Compiler\Llk\Parser
     */
    private $parser;

    public function parseDockblock(string $dockblock, Visit $visitor)
    {
        $parser  = $this->getParser();
        $ast     = $parser->parse($dockblock);
        return $visitor->visit($ast);
    }
    /**
     * @return \Hoa\Compiler\Llk\Parser
     */
    private function getParser()
    {
        if ($this->parser !== null) {
            return $this->parser;
        }
        $file   = new Read(__DIR__ . '/grammar.pp');
        $parser = Llk::load($file);
        return $this->parser = $parser;
    }
}
