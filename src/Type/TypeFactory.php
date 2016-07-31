<?php
namespace Dkplus\Reflections\Type;

use Dkplus\Reflections\Reflector;
use phpDocumentor\Reflection\Type as PhpDocumentorType;

interface TypeFactory
{
    public function create(Reflector $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type;
}
