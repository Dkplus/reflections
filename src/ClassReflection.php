<?php
declare(strict_types=1);

namespace Dkplus\Reflection;

interface ClassReflection
{
    public function name(): string;

    public function isFinal(): bool;

    public function isAbstract(): bool;

    public function isInvokable(): bool;

    public function isSubclassOf(string $className): bool;

    public function isCloneable(): bool;

    public function implementsInterface(string $className): bool;

    public function annotations(): Annotations;

    public function fileName(): string;

    public function properties(): Properties;

    public function methods(): Methods;
}
