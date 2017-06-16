<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\Fixtures;

/**
 * @OneAnnotation
 * @AnotherAnnotation()
 * @AnotherAnnotation({"foo": @OneAnnotation("bar")})
 * @AnotherAnnotation({"bar" = @OneAnnotation({"foo","bar"})})
 */
class ClassWithAnnotations
{
}
