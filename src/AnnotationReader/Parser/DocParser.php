<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Dkplus\Reflection\AnnotationReader\Builder;
use Dkplus\Reflection\AnnotationReader\Context;
use Dkplus\Reflection\AnnotationReader\Exception\ParserException;
use Dkplus\Reflection\AnnotationReader\Resolver;
use Hoa\Compiler\Exception as HoaException;

final class DocParser
{
    /** @var Resolver */
    protected $resolver;

    /** @var Builder */
    private $builder;

    /** @var HoaParser */
    private $parser;

    public function __construct(HoaParser $parser, Builder $builder, Resolver $resolver)
    {
        $this->parser   = $parser;
        $this->builder  = $builder;
        $this->resolver = $resolver;
    }

    public function parse(string $docblock, Context $context)
    {
        try {
            $ignoreNotImported = $context->getIgnoreNotImported();
            $visitor = new DocVisitor($context, $this->builder, $this->resolver, $ignoreNotImported);
            return $this->parser->parseDockblock($docblock, $visitor);
        } catch (HoaException $e) {
            throw ParserException::hoaException($e, $context->getDescription());
        }
    }
}
