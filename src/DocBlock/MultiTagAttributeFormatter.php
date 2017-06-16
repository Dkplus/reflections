<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\AttributeFormatter\FqsenAttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\MethodAttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\NamedAttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\RegexAttributeFormatter;
use Dkplus\Reflection\DocBlock\AttributeFormatter\TypeAttributeFormatter;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use function array_map;

class MultiTagAttributeFormatter
{
    /** @var array|AttributeFormatter[] */
    private $formattersByTag;

    public static function forDefaultTags(): self
    {
        $fqsenResolver = new FqsenResolver();
        $typeResolver = new TypeResolver($fqsenResolver);
        $justDescription = new NamedAttributeFormatter('description');
        return new self([
            'author' => new RegexAttributeFormatter('/(?P<name>[^<]+)(?:<(?P<emailaddress>[^>]+)>)?/'),
            'copyright' => $justDescription,
            'deprecated' => new RegexAttributeFormatter(
                '/(?P<version>\d+\.\d+.\d+)\s*(?P<description>.*)/',
                '/(?P<description>.*)/'
            ),
            'ignore' => $justDescription,
            'internal' => $justDescription,
            'license' => new RegexAttributeFormatter(
                '/(?P<url>(?:ftp|https?):\/\/[^\s]+)\s*(?P<name>.*)/',
                '/(?P<name>.*)/'
            ),
            'link' => new RegexAttributeFormatter('/(?P<uri>[^\s]+)\s*(?P<description>.*)/'),
            'method' => new TypeAttributeFormatter(new MethodAttributeFormatter(), $typeResolver),
            'package' => new NamedAttributeFormatter('name'),
            'param' => new TypeAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<type>[\S]+)\s*(?P<name>[\S]+)\s*(?P<description>.*)/'
            ), $typeResolver),
            'property' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<name>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'property-read' => new TypeAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<type>[\S]+)\s*(?P<name>[\S]+)\s*(?P<description>.*)/'
            ), $typeResolver),
            'property-write' => new TypeAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<type>[\S]+)\s*(?P<name>[\S]+)\s*(?P<description>.*)/'
            ), $typeResolver),
            'return' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'see' => new FqsenAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<uri>(?:ftp|https?):\/\/[\S]+)\s*(?P<description>.*)/',
                '/(?P<fqsen>[^\s]+\s?)\s*(?P<description>.*)/'
            ), $fqsenResolver),
            'since' => new RegexAttributeFormatter('/(?P<version>\d+\.\d+.\d+)\s*(?P<description>.*)/'),
            'subpackage' => new NamedAttributeFormatter('name'),
            'throws' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'todo' => $justDescription,
            'uses' => new FqsenAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<fqsen>[^\s]+\s?)\s*(?P<description>.*)/'
            ), $fqsenResolver),
            'var' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'version' => new RegexAttributeFormatter(
                '/^(?P<description>(?!\d+\.\d+\.\d+).*)/',
                '/(?P<vector>\d+\.\d+.\d+)?\s*(?P<description>.*)/'
            ),
            'covers' => new FqsenAttributeFormatter(new NamedAttributeFormatter('fqsen'), $fqsenResolver),
        ]);
    }

    /** @param AttributeFormatter[] $formatters */
    public function __construct(array $formatters)
    {
        $this->formattersByTag = array_map(function (AttributeFormatter $formatter) {
            return $formatter;
        }, $formatters);
    }

    public function format(string $tag, array $attributes, Context $context): array
    {
        if (isset($this->formattersByTag[$tag])) {
            return $this->formattersByTag[$tag]->format($attributes, $context);
        }
        return $attributes;
    }
}
