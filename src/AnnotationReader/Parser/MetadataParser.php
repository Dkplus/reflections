<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Dkplus\Reflection\AnnotationReader\Resolver;
use Doctrine\Common\Annotations\Annotation;

final class MetadataParser
{

    /** @var HoaParser */
    private $parser;

    /**
     * @var array
     */
    private $imports = [
        'annotation'       => Annotation::class,
        'type'             => 'Doctrine\Annotations\Annotation\Type',
        'enum'             => 'Doctrine\Annotations\Annotation\Enum',
        'target'           => 'Doctrine\Annotations\Annotation\Target',
        'ignoreannotation' => 'Doctrine\Annotations\Annotation\IgnoreAnnotation'
    ];

    public function __construct(HoaParser $parser, Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->parser   = $parser;
    }
    /**
     * @param \ReflectionClass $class
     *
     * @return array
     */
    public function parseAnnotationClass(ReflectionClass $class) : array
    {
        $docblock    = $class->getDocComment();
        $namespace   = $class->getNamespaceName();
        $annotations = $this->parseDockblock($class, $namespace, $docblock);
        return $annotations;
    }
    /**
     * @param \ReflectionProperty $property
     *
     * @return array
     */
    public function parseAnnotationProperty(ReflectionProperty $property) : array
    {
        $matches     = null;
        $docblock    = $property->getDocComment();
        $class       = $property->getDeclaringClass();
        $namespace   = $class->getNamespaceName();
        $annotations = $this->parseDockblock($property, $namespace, $docblock);
        if ($docblock && preg_match('/@var\s+([^\s]+)/', $docblock, $matches)) {
            $annotations[] = new Type(['value' => $matches[1]]);
        }
        return $annotations;
    }
    /**
     * @param \Reflector  $reflector
     * @param string      $namespace
     * @param string|bool $docblock
     *
     * @return array
     */
    private function parseDockblock(Reflector $reflector, $namespace, $docblock) : array
    {
        if ($docblock == false) {
            return [];
        }
        try {
            $context   = new Context($reflector, [], $this->imports);
            $visitor   = new MetadataVisitor($this->resolver, $context);
            $result    = $this->parser->parseDockblock($docblock, $visitor);
            return $result;
        } catch (\Hoa\Compiler\Exception $e) {
            throw ParserException::hoaException($e, $context->getDescription());
        }
    }
}
