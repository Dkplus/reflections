<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;

final class DocBlockReflector
{
    /** @var HoaParser */
    private $parser;

    /** @var AnnotationFactory */
    private $annotationFactory;

    public function __construct(
        ClassReflector $classReflector,
        FqsenResolver $fqsenResolver,
        MultiTagAttributeFormatter $attributeFormatter = null
    ) {
        $this->parser = new HoaParser();
        $this->annotationFactory = new AnnotationFactory($this, $classReflector, $fqsenResolver, $attributeFormatter);
    }

    public function reflectDocBlock(string $docBlock, Context $context): DocBlockReflection
    {
        return $this->parser->parseDockBlock(
            $docBlock,
            new DocBlockVisitor($this->annotationFactory, $context)
        );
    }
}
