<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation;

use Dkplus\Reflection\Annotation\AttributeFormatter\FqsenResolvingAttributeFormatter;
use Dkplus\Reflection\Annotation\AttributeFormatter\MethodAttributeFormatter;
use Dkplus\Reflection\Annotation\AttributeFormatter\NamedAttributeFormatter;
use Dkplus\Reflection\Annotation\AttributeFormatter\RegexAttributeFormatter;
use Dkplus\Reflection\Annotation\AttributeFormatter\TypeAttributeFormatter;
use Dkplus\Reflection\AnnotationReflection;
use Dkplus\Reflection\Exception\ClassNotFound;
use Dkplus\Reflection\ReflectorStrategy;
use InvalidArgumentException;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;

final class AnnotationFactory
{
    /** @var ReflectorStrategy */
    private $reflector;


    /** @var FqsenResolver */
    private $fqsenResolver;

    /** @var RegexAttributeFormatter[] */
    private $unqualifiedsValueFormatters;

    public function __construct(ReflectorStrategy $reflector, FqsenResolver $fqsenResolver)
    {
        $typeResolver = new TypeResolver($fqsenResolver);
        $this->reflector = $reflector;
        $this->fqsenResolver = $fqsenResolver;
        $this->unqualifiedsValueFormatters = [
            'author' => new RegexAttributeFormatter('/(?P<name>[^<]+)(?:<(?P<emailaddress>[^>]+)>)?/'),
            'copyright' => new NamedAttributeFormatter('description'),
            'deprecated' => new RegexAttributeFormatter(
                '/(?P<version>\d+\.\d+.\d+)\s*(?P<description>.*)/',
                '/(?P<description>.*)/'
            ),
            'ignore' => new NamedAttributeFormatter('description'),
            'internal' => new NamedAttributeFormatter('description'),
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
            'see' => new FqsenResolvingAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<uri>(?:ftp|https?):\/\/[\S]+)\s*(?P<description>.*)/',
                '/(?P<fqsen>[^\s]+\s?)\s*(?P<description>.*)/'
            ), $this->fqsenResolver),
            'since' => new RegexAttributeFormatter('/(?P<version>\d+\.\d+.\d+)\s*(?P<description>.*)/'),
            'subpackage' => new NamedAttributeFormatter('name'),
            'throws' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'todo' => new NamedAttributeFormatter('description'),
            'uses' => new FqsenResolvingAttributeFormatter(new RegexAttributeFormatter(
                '/(?P<fqsen>[^\s]+\s?)\s*(?P<description>.*)/'
            ), $this->fqsenResolver),
            'var' => new TypeAttributeFormatter(
                new RegexAttributeFormatter('/(?P<type>[\S]+)\s*(?P<description>.*)/'),
                $typeResolver
            ),
            'version' => new RegexAttributeFormatter(
                '/^(?P<description>(?!\d+\.\d+\.\d+).*)/',
                '/(?P<vector>\d+\.\d+.\d+)?\s*(?P<description>.*)/'
            ),
        ];
    }

    public function createReflection(string $identifier, array $attributes, Context $context): AnnotationReflection
    {
        try {
            $fqIdentifier = (string) $this->fqsenResolver->resolve($identifier, $context);
        } catch (InvalidArgumentException $exception) {
            return $this->unqualified($identifier, $attributes, $context);
        }

        try {
            $class = $this->reflector->reflectClass($fqIdentifier);
            return AnnotationReflection::fullyQualified($fqIdentifier, $attributes, $class);
        } catch (ClassNotFound $exception) {
        }

        return $this->unqualified($identifier, $attributes, $context);
    }

    private function unqualified(string $identifier, array $attributes, Context $context): AnnotationReflection
    {
        if (isset($this->unqualifiedsValueFormatters[$identifier])) {
            $attributes = $this->unqualifiedsValueFormatters[$identifier]->format($attributes, $context);
        }
        return AnnotationReflection::unqualified($identifier, $attributes);
    }
}
