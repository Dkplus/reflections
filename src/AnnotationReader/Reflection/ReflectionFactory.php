<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Reflection;

use Dkplus\Reflection\AnnotationReader\Parser\PhpParser;

/**
 * Reflection Factory
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ReflectionFactory
{
    /** @var ReflectionClass[] */
    private $classes = [];

    /** @var ReflectionMethod[] */
    private $methods = [];

    /** @var ReflectionProperty[] */
    private $properties = [];

    /** @var PhpParser */
    private $phpParser;

    public function __construct(PhpParser $phpParser)
    {
        $this->phpParser = $phpParser;
    }

    public function getReflectionClass(string $className): ReflectionClass
    {
        if (isset($this->classes[$className])) {
            return $this->classes[$className];
        }
        return $this->classes[$className] = new ReflectionClass($className, $this->phpParser);
    }

    public function getReflectionMethod(string $className, string $methodName): ReflectionMethod
    {
        $key = $className . '#' . $methodName;
        if (isset($this->methods[$key])) {
            return $this->methods[$key];
        }
        return $this->methods[$key] = new ReflectionMethod($className, $methodName, $this->phpParser);
    }

    public function getReflectionProperty(string $className, string $propertyName): ReflectionProperty
    {
        $key = $className . '$' . $propertyName;
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
        return $this->properties[$key] = new ReflectionProperty($className, $propertyName, $this->phpParser);
    }
}