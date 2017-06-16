<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use phpDocumentor\Reflection\Types\Context;

/** @package Dkplus\Reflection\DocBlock */
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
        return [$this->attributeName => $attributes[0] ?? ''];
    }
}
