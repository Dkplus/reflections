<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\NodeVisitor\ClassLikeNameCollector;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class FileReflector
{
    /** @var Parser */
    private $parser;

    /* @var NodeTraverser */
    private $traverser;

    /** @var ClassLikeNameCollector */
    private $classCollector;
    /**
     * @var Reflector
     */
    private $classReflector;

    public function __construct(Reflector $classReflector)
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this->classCollector = new ClassLikeNameCollector());
        $this->classReflector = $classReflector;
    }

    public function reflectClassesFromFile(string $file): array
    {
        $this->traverser->traverse($this->parser->parse(file_get_contents($file)));
        return array_map([$this->classReflector, 'reflectClassLike'], $this->classCollector->classNames());
    }
}
