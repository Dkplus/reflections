<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

class ClassWithTrait extends AnotherClassWithTrait
{
    use TraitUsesTrait;
}
