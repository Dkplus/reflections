<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use phpDocumentor\Reflection\Types\Context;

final class NamedAttributeFormatter implements AttributeFormatter
{
    /** @var string */
    private $attributeName;

    public function __construct(string $attributeName)
    {
        $this->attributeName = $attributeName;
    }

    public function format(array $attributes, Context $context): array
    {
        if (count($attributes) > 1 || ! is_string($attributes[0] ?? '')) {
            return $attributes;
        }
        return [$this->attributeName => $attributes[0] ?? ''];
    }
}
