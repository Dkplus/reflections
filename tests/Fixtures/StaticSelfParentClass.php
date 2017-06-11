<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

class StaticSelfParentClass
{
    /**
     * @return static
     */
    public static function staticReturn()
    {
    }

    /**
     * @return self
     */
    public static function selfReturn()
    {
    }
}
