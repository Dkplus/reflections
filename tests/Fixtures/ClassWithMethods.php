<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

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
}
