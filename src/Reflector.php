<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\NodeVisitor\ClassLikeNameCollector;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class Reflector
{
    /** @var Parser */
    private $parser;

    /* @var NodeTraverser */
    private $traverser;

    /** @var ClassLikeNameCollector */
    private $classCollector;

    /** @var ReflectorStrategy */
    private $strategy;

    public function __construct(ReflectorStrategy $strategy)
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this->classCollector = new ClassLikeNameCollector());
        $this->strategy = $strategy;
    }

    public function reflectClassesFromFile(string $file): array
    {
        $this->traverser->traverse($this->parser->parse(file_get_contents($file)));
        return array_map([$this->strategy, 'reflectClass'], $this->classCollector->classNames());
    }

    public function reflectClass(string $className): ClassReflection
    {
        return $this->strategy->reflectClass($className);
    }

    public function reflectMethod(string $className, string $method): MethodReflection
    {
        return $this->reflectClass($className)->method($method);
    }

    public function reflectProperty(string $className, string $property): PropertyReflection
    {
        return $this->reflectClass($className)->property($property);
    }
}
