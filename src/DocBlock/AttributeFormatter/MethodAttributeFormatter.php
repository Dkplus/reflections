<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock\AttributeFormatter;

use phpDocumentor\Reflection\Types\Context;
use function array_map;
use function array_shift;
use function preg_match;

final class MethodAttributeFormatter implements AttributeFormatter
{
    public function format(array $attributes, Context $context): array
    {
        if (count($attributes) > 1 || ! is_string($attributes[0] ?? '')) {
            return $attributes;
        }
        $regexp = '/(?P<return>[\S]*\s)?\s*(?P<name>[^\(]+)\((?P<params>[^\)]*)\)/';
        if (! preg_match($regexp, $attributes[0] ?? '', $matches)) {
            return $attributes;
        }
        $matches = array_map('trim', $matches);
        ['return' => $return, 'name' => $name, 'params' => $params] = $matches;
        if (trim($params) === '') {
            return ['return' => $this->type($return, 'void'), 'name' => $name, 'params' => []];
        }
        if (! preg_match_all('/(?P<types>[^\s\$]*)\s*(?P<params>\$[^,\)\s]+),?\s*/', $params, $matches)) {
            return $attributes;
        }
        ['types' => $types, 'params' => $paramNames] = $matches;
        $params = [];
        while ($types) {
            $params[] = [
                'type' => $this->type(trim(array_shift($types)), 'mixed'),
                'name' => trim(array_shift($paramNames)),
            ];
        }
        return ['return' => $this->type($return, 'void'), 'name' => $name, 'params' => $params];
    }

    private function type(string $type, string $fallback): string
    {
        return $type !== '' ? $type : $fallback;
    }
}
