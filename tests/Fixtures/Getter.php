<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Fixtures;

class Getter
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getName(): string
    {
        return $this->name;
    }
}
