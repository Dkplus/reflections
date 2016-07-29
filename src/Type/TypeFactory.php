<?php
namespace Dkplus\Reflections\Type;

use phpDocumentor\Reflection\Type as PhpDocumentorType;

interface TypeFactory
{
    public function create(PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type;
}
