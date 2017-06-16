<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock\Fixtures;

use RuntimeException;

/**
 * @author My Name
 * @author My Name <my.name@example.com>
 * @copyright 2017-2018 by some company
 * @deprecated
 * @deprecated 1.0.0
 * @deprecated 1.0.0 because we replaced it
 * @deprecated because we replaced it
 * @ignore
 * @ignore this tag
 * @internal
 * @internal again because we need a description
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link https://phpdoc.org/docs/latest/references/phpdoc/tags/link.html with description
 * @link https://phpdoc.org/docs/latest/references/phpdoc/tags/link.html
 * @method string getString()
 * @method setString(string $param1, $param2)
 * @package Foo\Bar
 * @subpackage Baz
 * @property string $property3
 * @property string $property4 with description
 * @property-read string $property5
 * @property-read string $property6 with description
 * @property-write string $property7
 * @property-write string $property8 with description
 * @see PhpDocAnnotations::$property1
 * @see PhpDocAnnotations::$property2 with description
 * @see http://example.org/
 * @see http://example.org/ description
 * @since 1.0.0
 * @since 1.0.0 with description
 * @source
 * @source with description
 * @source 40
 * @source 40 10
 * @source 40 with description
 * @source 40 10 with description
 * @todo something is missing
 * @uses PhpDocAnnotations::exampleFunction()
 * @uses PhpDocAnnotations with description
 * @version
 * @version 3.0.0
 * @version 3.0.0 with description
 */
final class PhpDocAnnotations
{
    /**
     * @var string
     */
    private $property1;

    /**
     * @var string with description
     */
    private $property2;

    /**
     * @param string $param1
     * @param string $param2 with description
     * @return void
     * @throws RuntimeException with description
     * @throws RuntimeException
     */
    public function exampleFunction(string $param1, string $param2)
    {
    }

    /**
     * @return void will return nothing
     */
    public function anotherExampleFunction()
    {
    }
}
