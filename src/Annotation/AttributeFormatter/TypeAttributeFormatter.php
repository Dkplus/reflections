<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation\AttributeFormatter;

use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;

final class TypeAttributeFormatter implements AttributeFormatter
{
    /** @var AttributeFormatter */
    private $decorated;

    /** @var TypeResolver */
    private $resolver;

    public function __construct(AttributeFormatter $decorated, TypeResolver $resolver)
    {
        $this->decorated = $decorated;
        $this->resolver = $resolver;
    }

    public function format(array $attributes, Context $context): array
    {
        $attributes = $this->decorated->format($attributes, $context);
        if (isset($attributes['type'])) {
            $attributes['type'] = $this->resolver->resolve($attributes['type'], $context);
        }
        if (isset($attributes['return'])) {
            $attributes['return'] = $this->resolver->resolve($attributes['return'], $context);
        }
        if (isset($attributes['params'])) {
            array_walk($attributes['params'], function (array &$param) use ($context) {
                $param = $this->format($param, $context);
            });
        }
        return $attributes;
    }
}
