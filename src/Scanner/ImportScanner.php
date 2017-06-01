<?php
namespace Dkplus\Reflection\Scanner;

use Zend\Code\Scanner\FileScanner;

/**
 * @internal
 */
class ImportScanner
{
    public function scanForImports(string $fileName): array
    {
        $imports = (new FileScanner($fileName))->getUses();
        array_walk($imports, function (array &$import) {
            if ($import['as'] === null) {
                $namespaceParts = explode('\\', $import['use']);
                $import['as'] = array_pop($namespaceParts);
            }
        });
        return array_combine(
            array_column($imports, 'as'),
            array_column($imports, 'use')
        );
    }
}
