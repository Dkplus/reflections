<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\ClassReflection;

final class AnnotationReflection
{
    /** @var string */
    private $name;

    /** @var bool */
    private $fullyQualified = false;

    /** @var ClassReflection */
    private $class;

    /** @var mixed */
    private $values;

    public static function fullyQualified(string $name, $values, ClassReflection $class): self
    {
        $result = new self($name, $values);
        $result->fullyQualified = true;
        $result->class = $class;
        return $result;
    }

    public static function unqualified(string $name, $values): self
    {
        return new self($name, $values);
    }

    private function __construct(string $name, $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isFullyQualified(): bool
    {
        return $this->fullyQualified;
    }

    public function class(): ?ClassReflection
    {
        return $this->class;
    }

    public function attributes()
    {
        return $this->values;
    }
}
