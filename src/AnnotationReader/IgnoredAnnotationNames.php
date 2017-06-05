<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader;

use ArrayObject;

/**
 *  A list with annotations that are not causing exceptions when not resolved to an annotation class.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
final class IgnoredAnnotationNames extends ArrayObject
{
    /**
     * The names are case sensitive.
     */
    const DEFAULT_NAMES = [
        // Annotation tags
        'Annotation' => true,
        'IgnoreAnnotation' => true,
        /* Can we enable this? 'Enum' => true, */
        'Required' => true,
        'Target' => true,
        // Widely used tags (but not existent in phpdoc)
        'fix' => true,
        'fixme' => true,
        'override' => true,
        // PHPDocumentor 1 tags
        'abstract' => true,
        'access' => true,
        'code' => true,
        'deprec' => true,
        'endcode' => true,
        'exception' => true,
        'final' => true,
        'ingroup' => true,
        'inheritdoc' => true,
        'inheritDoc' => true,
        'magic' => true,
        'name' => true,
        'toc' => true,
        'tutorial' => true,
        'private' => true,
        'static' => true,
        'staticvar' => true,
        'staticVar' => true,
        'throw' => true,
        // PHPDocumentor 2 tags.
        'api' => true,
        'author' => true,
        'category' => true,
        'copyright' => true,
        'deprecated' => true,
        'example' => true,
        'filesource' => true,
        'global' => true,
        'ignore' => true, /* Can we enable this? 'index' => true, */
        'internal' => true,
        'license' => true,
        'link' => true,
        'method' => true,
        'package' => true,
        'param' => true,
        'property' => true,
        'property-read' => true,
        'property-write' => true,
        'return' => true,
        'see' => true,
        'since' => true,
        'source' => true,
        'subpackage' => true,
        'throws' => true,
        'todo' => true,
        'TODO' => true,
        'usedby' => true,
        'uses' => true,
        'var' => true,
        'version' => true,
        // PHPUnit tags
        'codeCoverageIgnore' => true,
        'codeCoverageIgnoreStart' => true,
        'codeCoverageIgnoreEnd' => true,
        // PHPCheckStyle
        'SuppressWarnings' => true,
        // PHPStorm
        'noinspection' => true,
        // PEAR
        'package_version' => true,
        // PlantUML
        'startuml' => true,
        'enduml' => true,
    ];
}
