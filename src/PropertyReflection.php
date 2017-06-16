<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

use Dkplus\Reflection\DocBlock\Annotations;
use Dkplus\Reflection\DocBlock\DocBlockReflection;
use Dkplus\Reflection\Type\Type;
use ReflectionProperty;

class PropertyReflection
{
    /** @var ReflectionProperty */
    private $reflection;

    /** @var DocBlockReflection */
    private $docBlock;

    /** @var Type */
    private $type;

    /** @internal */
    public function __construct(ReflectionProperty $reflection, Type $type, DocBlockReflection $docBlock)
    {
        $this->reflection = $reflection;
        $this->type = $type;
        $this->docBlock = $docBlock;
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

    public function type(): Type
    {
        return $this->type;
    }

    public function allows(Type $type): bool
    {
        return $this->type->accepts($type);
    }

    public function docBlock(): DocBlockReflection
    {
        return $this->docBlock;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }
}
