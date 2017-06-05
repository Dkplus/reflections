<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use SplFileObject;
use ReflectionClass;

/**
 * Parses a file for namespaces/use/class declarations.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Christian Kaps <christian.kaps@mohiva.com>
 */
final class PhpParser
{
    /**
     * Parses a class.
     *
     * @param \ReflectionClass $class A <code>ReflectionClass</code> object.
     *
     * @return array A list with use statements in the form (Alias => FQN).
     */
    public function parseClass(ReflectionClass $class) : array
    {
        if (method_exists($class, 'getUseStatements')) {
            return $class->getUseStatements();
        }
        $lineNumber = $class->getStartLine();
        $filename   = $class->getFilename();
        $content    = ($filename !== false)
            ? $this->getFileContent($filename, $lineNumber)
            : null;
        if ($content === null) {
            return [];
        }
        $namespace = preg_quote($class->getNamespaceName());
        $regex     = '/^.*?(\bnamespace\s+' . $namespace . '\s*[;{].*)$/s';
        $content   = preg_replace($regex, '\\1', $content);
        $tokenizer = new TokenParser('<?php ' . $content);
        return $tokenizer->parseUseStatements($class->getNamespaceName());
    }
    /**
     * Gets the content of the file right up to the given line number.
     *
     * @param string  $filename   The name of the file to load.
     * @param integer $lineNumber The number of lines to read from file.
     *
     * @return string The content of the file.
     */
    private function getFileContent(string $filename, int $lineNumber)
    {
        if ( ! is_file($filename)) {
            return null;
        }
        $lineCnt = 0;
        $content = '';
        $file    = new SplFileObject($filename);
        while ( ! $file->eof() && $lineCnt++ < $lineNumber) {
            $content .= $file->fgets();
        }
        return $content;
    }
}