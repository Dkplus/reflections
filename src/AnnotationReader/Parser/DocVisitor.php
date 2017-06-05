<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Dkplus\Reflection\AnnotationReader\Builder;
use Dkplus\Reflection\AnnotationReader\Context;
use Dkplus\Reflection\AnnotationReader\Exception\ClassNotFoundException;
use Dkplus\Reflection\AnnotationReader\Reference;
use Dkplus\Reflection\AnnotationReader\Resolver;

final class DocVisitor extends BaseVisitor
{
    /** @var Resolver */
    protected $resolver;

    /** @var Builder */
    private $builder;

    /** @var Context */
    private $context;

    /**
     * Whether annotations that have not been imported should be ignored.
     *
     * @var bool
     */
    private $ignoreNotImported;

    public function __construct(Context $context, Builder $builder, Resolver $resolver, bool $ignoreNotImported = false)
    {
        $this->builder = $builder;
        $this->context = $context;
        $this->resolver = $resolver;
        $this->ignoreNotImported = $ignoreNotImported;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveClass(string $class): string
    {
        return $this->resolver->resolve($this->context, $class);
    }

    /**
     * {@inheritdoc}
     */
    protected function createAnnotation(Reference $reference)
    {
        if ($this->context->isIgnoredName($reference->name)) {
            return null;
        }
        if (! $this->ignoreNotImported) {
            return $this->builder->create($this->context, $reference);
        }
        try {
            return $this->builder->create($this->context, $reference);
        } catch (ClassNotFoundException $e) {
            return null;
        }
    }
}
