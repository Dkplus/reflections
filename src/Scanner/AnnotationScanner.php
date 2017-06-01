<?php
namespace Dkplus\Reflection\Scanner;

use Dkplus\Reflection\Annotations;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;

/**
 * @internal
 */
class AnnotationScanner
{
    public function scanForAnnotations(string $docBlock, $context, array $imports): Annotations
    {
        // Hack to ensure an attempt to autoload an annotation class is made
        AnnotationRegistry::registerLoader(function ($class) {
            return (bool) class_exists($class);
        });
        $imports = array_combine(
            array_map(function ($string) { return strtolower($string); }, array_keys($imports)),
            array_values($imports)
        );
        $parser = new DocParser();
        $parser->setIgnoreNotImportedAnnotations(true);
        $parser->setImports($imports);
        return new Annotations($parser->parse($docBlock, $context));
    }
}
