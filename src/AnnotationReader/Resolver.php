<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use Dkplus\Reflection\AnnotationReader\Exception\ClassNotFoundException;

final class Resolver
{
    public function resolve(Context $context, string $className): string
    {
        $isFullyQualified = '\\' === $className[0];
        $contextDescription = $context->getDescription();
        if ($isFullyQualified && $this->classExists($className)) {
            return $className;
        }
        if ($isFullyQualified) {
            throw ClassNotFoundException::annotationNotFound($className, $contextDescription);
        }
        if (($fqcn = $this->resolveImports($className, $context->getImports())) !== null) {
            return $fqcn;
        }
        if (($fqcn = $this->resolveNamespaces($className, $context->getNamespaces())) !== null) {
            return $fqcn;
        }
        if ($this->classExists($className)) {
            return $className;
        }
        throw ClassNotFoundException::annotationNotImported($className, $contextDescription);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function classExists(string $class): bool
    {
        return class_exists($class) || interface_exists($class);
    }

    /**
     * @param string $class
     * @param array $namespaces
     *
     * @return string|null
     */
    private function resolveNamespaces(string $class, array $namespaces)
    {
        foreach ($namespaces as $namespace) {
            if ($this->classExists($namespace . '\\' . $class)) {
                return $namespace . '\\' . $class;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $imports
     *
     * @return string|null
     */
    private function resolveImports(string $name, array $imports)
    {
        $index = strpos($name, '\\');
        $alias = strtolower($name);
        if ($index !== false) {
            $part = substr($name, 0, $index);
            $alias = strtolower($part);
        }
        if (! isset($imports[$alias])) {
            return null;
        }
        $class = ($index !== false)
            ? $imports[$alias] . substr($name, $index)
            : $imports[$alias];
        if (! $this->classExists($class)) {
            return null;
        }
        return $class;
    }
}
