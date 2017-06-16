<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use phpDocumentor\Reflection\Types\Context;

/** @package Dkplus\Reflection\DocBlock */
final class RegexAttributeFormatter implements AttributeFormatter
{
    /** @var string[] */
    private $regex;

    public function __construct(string ...$regex)
    {
        $this->regex = $regex;
    }

    public function format(array $attributes, Context $context): array
    {
        $text = $attributes[0] ?? '';
        foreach ($this->regex as $each) {
            if (preg_match($each, $text, $matches)) {
                array_shift($matches);
                return array_map('trim', array_filter($matches, function ($key) {
                    return ! is_numeric($key);
                }, ARRAY_FILTER_USE_KEY));
            }
        }
        return $attributes;
    }
}
