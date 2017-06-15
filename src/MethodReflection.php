<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\Type\Type;
use ReflectionMethod;

/**
 * @api
 */
class MethodReflection
{
    /** @var ReflectionMethod */
    private $reflection;

    /** @var Annotations */
    private $annotations;

    /** @var Type */
    private $returnType;

    /** @var Parameters */
    private $parameters;

    /** @internal */
    public function __construct(
        ReflectionMethod $reflection,
        Annotations $annotations,
        Parameters $parameters,
        Type $returnType
    ) {
        $this->reflection = $reflection;
        $this->annotations = $annotations;
        $this->returnType = $returnType;
        $this->parameters = $parameters;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function returnType(): Type
    {
        return $this->returnType;
    }

    public function isGetterOf(string $property): bool
    {
        return $this->countParameters() === 0
            && preg_match("/^return [^;]*\\\$this->{$property}[^;]*;$/", $this->reflection->getBodyCode());
    }

    public function countParameters(): int
    {
        return $this->reflection->getNumberOfParameters();
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }

    public function allowsToBePassed(Type ...$types): bool
    {
        return $this->parameters()->allows(...$types);
    }

    public function annotations(): Annotations
    {
        return $this->annotations;
    }
}
