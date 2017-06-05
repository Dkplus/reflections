<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation;

final class Context
{
    /** @var string */
    private $namespace;

    /** @var string[] */
    private $imports;

    public function __construct(string $namespace, array $imports)
    {
        $this->namespace = $namespace;
        $this->imports = $imports;
    }

    public function imports(): array
    {
        return $this->imports;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }
}
