<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use Dkplus\Reflection\DocBlock\AttributeFormatter;
use phpDocumentor\Reflection\Types\Context;
use function array_map;
use function array_shift;
use function preg_match;

/** @package Dkplus\Reflection\DocBlock */
final class MethodAttributeFormatter implements AttributeFormatter
{
    public function format(array $attributes, Context $context): array
    {
        $input = $attributes[0] ?? '';
        $regexp = '/(?P<return>[\S]*\s)?\s*(?P<name>[^\(]+)\((?P<params>[^\)]*)\)/';
        if (! preg_match($regexp, $input, $matches)) {
            return $attributes;
        }
        $matches = array_map('trim', $matches);
        ['return' => $return, 'name' => $name, 'params' => $params] = $matches;
        $params = $this->formatParameters($params);
        if ($params === null) {
            return $attributes;
        }
        return ['return' => $this->type($return, 'void'), 'name' => $name, 'params' => $params];
    }

    private function formatParameters(string $params): ?array
    {
        if (trim($params) === '') {
            return [];
        }
        if (! preg_match_all('/(?P<types>[^\s\$]*)\s*(?P<params>\$[^,\)\s]+),?\s*/', $params, $matches)) {
            return null;
        }
        ['types' => $types, 'params' => $paramNames] = $matches;
        $result = [];
        while ($types) {
            $result[] = [
                'type' => $this->type(trim(array_shift($types)), 'mixed'),
                'name' => trim(array_shift($paramNames)),
            ];
        }
        return $result;
    }

    private function type(string $type, string $fallback): string
    {
        return $type !== '' ? $type : $fallback;
    }
}
