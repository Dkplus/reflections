<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Annotation;

final class Type
{

    /**
     * Hash-map for handle types declaration.
     *
     * @var array
     */
    private static $typeMap = [
        'float'     => 'double',
        'bool'      => 'boolean',
        // allow uppercase Boolean in honor of George Boole
        'Boolean'   => 'boolean',
        'int'       => 'integer',
    ];
    /**
     * @var string
     */
    public $type = 'mixed';
    /**
     * @var string
     */
    public $arrayType;
    /**
     * Annotation constructor.
     *
     * @param array $values
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if ( ! isset($values['value'])) {
            return;
        }
        $matches   = null;
        $arrayType = null;
        $type      = trim($values['value']);
        if (isset(self::$typeMap[$type])) {
            $type = self::$typeMap[$type];
        }
        // Checks if the property has array<type>
        if (($pos = strpos($type, '<')) !== false) {
            $arrayType  = substr($type, $pos + 1, -1);
            $type       = 'array';
            if (isset(self::$typeMap[$arrayType])) {
                $arrayType = self::$typeMap[$arrayType];
            }
        }
        // Checks if the property has type[]
        if (($pos = strpos($type, '[')) !== false) {
            $arrayType  = substr($type, 0, $pos);
            $type       = 'array';
            if (isset(self::$typeMap[$arrayType])) {
                $arrayType = self::$typeMap[$arrayType];
            }
        }
        $this->type      = $type;
        $this->arrayType = $arrayType;
    }
}
