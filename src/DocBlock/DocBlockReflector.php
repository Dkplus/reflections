<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use InvalidArgumentException;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use Reflector;
use function get_class;
use function method_exists;

final class DocBlockReflector
{
    /** @var HoaParser */
    private $parser;

    /** @var AnnotationFactory */
    private $annotationFactory;

    /** @var ClassReflector */
    private $classReflector;

    /** @var FqsenResolver */
    private $fqsenResolver;

    public function __construct(
        ClassReflector $classReflector,
        FqsenResolver $fqsenResolver,
        MultiTagAttributeFormatter $attributeFormatter = null
    ) {
        $this->parser = new HoaParser();
        $this->fqsenResolver = $fqsenResolver;
        $this->classReflector = $classReflector;
        $this->annotationFactory = new AnnotationFactory($this, $classReflector, $fqsenResolver, $attributeFormatter);
    }

    public function reflectDocBlock(string $docBlock, Context $context): DocBlockReflection
    {
        return $this->parser->parseDockBlock(
            $docBlock,
            new DocBlockVisitor($this->annotationFactory, $context, $this->classReflector, $this->fqsenResolver)
        );
    }

    public function reflectDocBlockFromReflector(Reflector $reflector, Context $context = null): DocBlockReflection
    {
        if (! $context) {
            $context = (new ContextFactory())->createFromReflector($reflector);
        }
        if (! method_exists($reflector, 'getDocComment')) {
            throw new InvalidArgumentException('Class ' . get_class($reflector) . ' provides no doc comment');
        }
        return $this->reflectDocBlock((string) $reflector->getDocComment(), $context);
    }
}
