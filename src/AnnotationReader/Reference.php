<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

final class Reference
{
    /** @var string */
    public $name;

    /** @var array */
    public $values;

    /** @var bool */
    public $nested;

    public function __construct(string $name, array $values = [], bool $nested = false)
    {
        $this->name = $name;
        $this->values = $values;
        $this->nested = $nested;
    }
}
