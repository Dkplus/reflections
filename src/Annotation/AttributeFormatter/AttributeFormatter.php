<?php
declare(strict_types=1);

namespace Dkplus\Reflection\Annotation\AttributeFormatter;

use phpDocumentor\Reflection\Types\Context;

interface AttributeFormatter
{
    public function format(array $attributes, Context $context): array;
}
