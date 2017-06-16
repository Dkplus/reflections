<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\Fixtures;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target(Target::TARGET_CLASS)
 */
final class AnotherAnnotation
{
}
