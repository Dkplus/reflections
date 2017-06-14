<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

use function null;

abstract class ClassWithMethods
{
    public static function staticMethod()
    {
    }

    public function oneMethod()
    {
    }

    final public function finalMethod()
    {
    }

    public function publicMethod()
    {
    }

    protected function protectedMethod()
    {
    }

    private function privateMethod()
    {
    }

    abstract public function abstractMethod();

    public function noReturnType()
    {
    }

    public function stringReturnType(): string
    {
        return 'foo';
    }

    /** @return string */
    public function stringReturnTag()
    {
        return 'bar';
    }

    /** @return OneClass */
    public function returnsObject(): OneClass
    {
        return new OneClass();
    }

    public function methodWithParameters($value)
    {
    }

    /**
     * @param string $docBlock
     */
    public function methodWithAdditionalDocBlockTags($value)
    {
    }

    /** @param string $stringParam */
    public function methodWithDocTypeParameter($stringParam)
    {
    }

    public function methodWithTypeHintParameter(string $stringParam)
    {
    }

    /** @param string[] $stringArrayParam */
    public function methodWithDocTypeAndTypeHintParameter(array $stringArrayParam)
    {
    }

    /** @param string $parameter Parameter description */
    public function methodWithParameterDescription(string $parameter)
    {
    }

    public function methodWithOmittableParameter(string $nonOmittable, string $omittable = null, string $omittableWithStringDefault = 'foo')
    {
    }

    public function methodWithVariadic(string $nonVariadic, string ...$variadic)
    {
    }

    /** @param string[] ...$variadic */
    public function methodWithVariadicAndDocBlock(string ...$variadic)
    {
    }
}
