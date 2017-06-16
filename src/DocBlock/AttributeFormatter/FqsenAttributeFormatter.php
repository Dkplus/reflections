<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context;

/** @package Dkplus\Reflection\DocBlock */
final class FqsenAttributeFormatter implements AttributeFormatter
{
    /** @var AttributeFormatter */
    private $decorated;

    /** @var FqsenResolver */
    private $fqsenResolver;

    public function __construct(AttributeFormatter $decorated, FqsenResolver $fqsenResolver)
    {
        $this->decorated = $decorated;
        $this->fqsenResolver = $fqsenResolver;
    }

    public function format(array $attributes, Context $context): array
    {
        $attributes = $this->decorated->format($attributes, $context);
        if (isset($attributes['fqsen'])) {
            $attributes['fqsen'] = $this->fqsenResolver->resolve($attributes['fqsen'], $context);
        }
        return $attributes;
    }
}
