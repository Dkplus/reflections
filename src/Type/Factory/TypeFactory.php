<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Type\Factory;

use Dkplus\Reflection\Type\ComposedType;
use Dkplus\Reflection\Type\MixedType;
use Dkplus\Reflection\Type\Type;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type as PhpDocType;

class TypeFactory
{
    /** @var TypeConverter */
    private $converter;

    /** * @var TypeNormalizer */
    private $normalizer;

    public function __construct(TypeConverter $converter, TypeNormalizer $normalizer)
    {
        $this->converter = $converter;
        $this->normalizer = $normalizer;
    }

    public function create(PhpDocType $typeHint, PhpDocType $docType, Fqsen $context): Type
    {
        $convertedTypeHint = $this->normalizer->normalize($this->converter->convert($typeHint, $context));
        $convertedDocType = $this->normalizer->normalize($this->converter->convert($docType, $context));
        if ($convertedTypeHint instanceof MixedType) {
            return $convertedDocType;
        }
        if ($convertedDocType instanceof MixedType) {
            return $convertedTypeHint;
        }
        return $this->normalizer->normalize(new ComposedType($convertedTypeHint, $convertedDocType));
    }
}
