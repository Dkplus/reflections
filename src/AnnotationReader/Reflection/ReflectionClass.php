<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Reflection;

use Dkplus\Reflection\AnnotationReader\Parser\PhpParser;

/**
 * Reflection Class
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ReflectionClass extends \ReflectionClass
{
    /** @var PhpParser */
    private $phpParser;

    /**
     * @var array
     */
    private $imports;

    public function __construct(string $className, PhpParser $phpParser)
    {
        parent::__construct($className);
        $this->phpParser = $phpParser;
    }

    /**
     * @return array
     */
    public function getImports(): array
    {
        if ($this->imports !== null) {
            return $this->imports;
        }
        return $this->imports = $this->phpParser->parseClass($this);
    }
}