<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation;

use Dkplus\Reflection\Annotations;

final class AnnotationReflector
{
    /** @var HoaParser */
    private $parser;

    /** @var AnnotationFactory */
    private $annotationFactory;

    public function __construct(HoaParser $parser, AnnotationFactory $annotationFactory)
    {
        $this->parser = $parser;
        $this->annotationFactory = $annotationFactory;
    }

    public function reflectDocBlock($docBlock, Context $context): Annotations
    {
        $annotations = $this->parser->parseDockBlock(
            $docBlock,
            new AnnotationVisitor($this->annotationFactory, $context)
        );
        return new Annotations($annotations);
    }
}
