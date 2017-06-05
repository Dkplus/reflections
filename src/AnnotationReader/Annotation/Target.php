<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Annotation;

final class Target
{
    const TARGET_CLASS = 1;
    const TARGET_METHOD = 2;
    const TARGET_PROPERTY = 4;
    const TARGET_ANNOTATION = 8;
    const TARGET_ALL = 15;
    /**
     * @var array
     */
    private static $map = [
        'ALL' => self::TARGET_ALL,
        'CLASS' => self::TARGET_CLASS,
        'METHOD' => self::TARGET_METHOD,
        'PROPERTY' => self::TARGET_PROPERTY,
        'ANNOTATION' => self::TARGET_ANNOTATION,
    ];
    /**
     * @var array
     */
    public $value;
    /**
     * Target as bitmask.
     *
     * @var integer
     */
    public $target;

    /**
     * Annotation constructor.
     *
     * @param array $values
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (! isset($values['value'])) {
            $values['value'] = null;
        }
        if (is_string($values['value'])) {
            $values['value'] = [$values['value']];
        }
        if (! is_array($values['value'])) {
            throw new \InvalidArgumentException(sprintf(
                '@Target expects either a string value, or an array of strings, "%s" given.',
                is_object($values['value']) ? get_class($values['value']) : gettype($values['value'])
            ));
        }
        $bitmask = 0;
        foreach ($values['value'] as $literal) {
            if (! isset(self::$map[$literal])) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid Target "%s". Available targets: [%s]',
                    $literal,
                    implode(', ', array_keys(self::$map))
                ));
            }
            $bitmask |= self::$map[$literal];
        }
        $this->target = $bitmask;
        $this->value = $values['value'];
    }

    /**
     * @param int $target
     *
     * @return array
     */
    public static function getNames(int $target): array
    {
        $names = [];
        if ($target & self::TARGET_CLASS) {
            $names[] = 'CLASS';
        }
        if ($target & self::TARGET_METHOD) {
            $names[] = 'METHOD';
        }
        if ($target & self::TARGET_PROPERTY) {
            $names[] = 'PROPERTY';
        }
        if ($target & self::TARGET_ANNOTATION) {
            $names[] = 'ANNOTATION';
        }
        return $names;
    }
}