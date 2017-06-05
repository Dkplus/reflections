<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Dkplus\Reflection\AnnotationReader\Context;
use Dkplus\Reflection\AnnotationReader\Exception\ClassNotFoundException;
use Dkplus\Reflection\AnnotationReader\Reference;
use Dkplus\Reflection\AnnotationReader\Resolver;

final class MetadataVisitor extends BaseVisitor
{
    /** @var Resolver */
    protected $resolver;

    /** @var Context */
    protected $context;

    public function __construct(Resolver $resolver, Context $context)
    {
        $this->resolver = $resolver;
        $this->context  = $context;
    }

    protected function resolveClass(string $class) : string
    {
        return $this->resolver->resolve($this->context, $class);
    }

    protected function createAnnotation(Reference $reference)
    {
        try {
            $fullClass  = $this->resolver->resolve($this->context, $reference->name);
            $isMetadata = strpos($fullClass, 'Doctrine\Annotations\Annotation') === 0;
            if ( ! $isMetadata) {
                return null;
            }
            return new $fullClass($reference->values);
        } catch (ClassNotFoundException $e) {
            return null;
        }
    }
}
