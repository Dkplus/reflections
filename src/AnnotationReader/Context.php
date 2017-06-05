<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use Reflector;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Doctrine\Common\Annotations\Annotation\Target;
use RuntimeException;

final class Context
{
    /**
     * A list with annotations that are not causing exceptions when not resolved to an annotation class.
     *
     * @var string[]
     */
    private $ignoredNames = [];

    /**
     * Property, method or class been parsed
     *
     * @var Reflector
     */
    private $reflection;

    /**
     * List of namespaces.
     *
     * @var string[]
     */
    private $namespaces;

    /**
     * A map with use statements in the form (Alias => FQN).
     *
     * @var string[]
     */
    private $imports;

    /**
     * Whether annotations that have not been imported should be ignored.
     *
     * @var bool
     */
    private $ignoreNotImported;

    public function __construct(
        Reflector $reflection,
        array $namespaces,
        array $imports = [],
        array $ignoredNames = [],
        bool $ignoreNotImported = false
    ) {
        $this->ignoreNotImported = $ignoreNotImported;
        $this->ignoredNames = $ignoredNames;
        $this->reflection = $reflection;
        $this->namespaces = $namespaces;
        $this->imports = $imports;
    }

    public function getIgnoreNotImported(): bool
    {
        return $this->ignoreNotImported;
    }

    /** @return string[] */
    public function getIgnoredNames(): array
    {
        return $this->ignoredNames;
    }

    public function isIgnoredName(string $name): bool
    {
        return isset($this->ignoredNames[$name]);
    }

    public function getReflection(): Reflector
    {
        return $this->reflection;
    }

    /** @return string[] */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /** @return string[] */
    public function getImports(): array
    {
        return $this->imports;
    }

    public function getTarget(): int
    {
        if ($this->reflection instanceof ReflectionClass) {
            return Target::TARGET_CLASS;
        }
        if ($this->reflection instanceof ReflectionMethod) {
            return Target::TARGET_METHOD;
        }
        if ($this->reflection instanceof ReflectionProperty) {
            return Target::TARGET_PROPERTY;
        }
        throw new RuntimeException('Unsupported target : ' . get_class($this->reflection));
    }

    public function getDescription(): string
    {
        if ($this->reflection instanceof ReflectionClass) {
            $name = $this->reflection->getName();
            $context = 'class ' . $name;
            return $context;
        }
        if ($this->reflection instanceof ReflectionMethod) {
            $name = $this->reflection->getName();
            $class = $this->reflection->getDeclaringClass();
            $context = 'method ' . $class->getName() . '::' . $name . '()';
            return $context;
        }
        if ($this->reflection instanceof ReflectionProperty) {
            $name = $this->reflection->getName();
            $class = $this->reflection->getDeclaringClass();
            $context = 'property ' . $class->getName() . '::$' . $name;
            return $context;
        }
        throw new RuntimeException('Unsupported target : ' . get_class($this->reflection));
    }
}
