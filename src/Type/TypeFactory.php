<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type;

use Dkplus\Reflection\Reflector;
use phpDocumentor\Reflection\Type as PhpDocumentorType;

interface TypeFactory
{
    public function create(Reflector $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type;
}
