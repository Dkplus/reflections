<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use Dkplus\Reflection\AnnotationReader\Reflection\ReflectionFactory;
use Dkplus\Reflection\AnnotationReader\Metadata\MetadataFactory;
use Dkplus\Reflection\AnnotationReader\Parser\MetadataParser;
use Dkplus\Reflection\AnnotationReader\Parser\DocParser;
use Dkplus\Reflection\AnnotationReader\Parser\HoaParser;
use Dkplus\Reflection\AnnotationReader\Parser\PhpParser;

class Configuration
{
    /**
     * A list with annotations that are not causing exceptions when not resolved to an annotation class.
     *
     * The names must be the raw names as used in the class, not the fully qualified
     * class names.
     *
     * @var IgnoredAnnotationNames
     */
    private $ignoredAnnotationNames;

    /** @var ReflectionFactory */
    private $reflectionFactory;

    /** @var MetadataFactory */
    private $metadataFactory;

    /** @var MetadataParser */
    private $metadataParser;

    /** DocParser */
    private $docParser;

    /** PhpParser */
    private $phpParser;

    /** HoaParser */
    private $hoaParser;

    /** @var Resolver */
    private $resolver;

    /** Builder */
    private $builder;

    public function setIgnoredAnnotationNames(IgnoredAnnotationNames $names)
    {
        $this->ignoredAnnotationNames = $names;
    }

    public function getIgnoredAnnotationNames() : IgnoredAnnotationNames
    {
        if ($this->ignoredAnnotationNames !== null) {
            return $this->ignoredAnnotationNames;
        }

        return $this->ignoredAnnotationNames = new IgnoredAnnotationNames(IgnoredAnnotationNames::DEFAULT_NAMES);
    }

    public function setMetadataFactory(MetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    public function getMetadataFactory() : MetadataFactory
    {
        if ($this->metadataFactory !== null) {
            return $this->metadataFactory;
        }

        return $this->metadataFactory = new MetadataFactory($this->getMetadataParser());
    }

    public function setReflectionFactory(ReflectionFactory $factory)
    {
        $this->reflectionFactory = $factory;
    }

    public function getReflectionFactory() : ReflectionFactory
    {
        if ($this->reflectionFactory !== null) {
            return $this->reflectionFactory;
        }

        return $this->reflectionFactory = new ReflectionFactory($this->getPhpParser());
    }

    public function setPhpParser(PhpParser $parser)
    {
        $this->phpParser = $parser;
    }

    public function getPhpParser() : PhpParser
    {
        if ($this->phpParser !== null) {
            return $this->phpParser;
        }

        return $this->phpParser = new PhpParser();
    }

    public function setHoaParser(HoaParser $parser)
    {
        $this->hoaParser = $parser;
    }

    public function getHoaParser() : HoaParser
    {
        if ($this->hoaParser !== null) {
            return $this->hoaParser;
        }

        return $this->hoaParser = new HoaParser();
    }

    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver() : Resolver
    {
        if ($this->resolver !== null) {
            return $this->resolver;
        }

        return $this->resolver = new Resolver();
    }

    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getBuilder() : Builder
    {
        if ($this->builder !== null) {
            return $this->builder;
        }

        $resolver = $this->getResolver();
        $factory  = $this->getMetadataFactory();
        $builder  = new Builder($resolver, $factory);

        return $this->builder = $builder;
    }

    public function setMetadataParser(MetadataParser $parser)
    {
        $this->metadataParser = $parser;
    }

    public function getMetadataParser() : MetadataParser
    {
        if ($this->metadataParser !== null) {
            return $this->metadataParser;
        }

        $resolver  = $this->getResolver();
        $hoaParser = $this->getHoaParser();
        $parser    = new MetadataParser($hoaParser, $resolver);

        return $this->metadataParser = $parser;
    }

    public function setDocParser(DocParser $parser)
    {
        $this->docParser = $parser;
    }

    public function getDocParser() : DocParser
    {
        if ($this->docParser !== null) {
            return $this->docParser;
        }

        $builder   = $this->getBuilder();
        $resolver  = $this->getResolver();
        $hoaParser = $this->getHoaParser();
        $parser    = new DocParser($hoaParser, $builder, $resolver);

        return $this->docParser = $parser;
    }
}