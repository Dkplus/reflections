<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

class ClassWithProperties
{
    public static $staticProperty;
    private $privateProperty;
    protected $protectedProperty;
    public $publicProperty;
    /** @var string */
    private $propertyWithType;
}
