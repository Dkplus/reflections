<?php
namespace spec\Dkplus\Reflection\Mock;

use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\ClassReflection_;
use Dkplus\Reflection\Methods;
use Dkplus\Reflection\Properties;

final class ClassReflectionStub implements ClassReflection_
{
    /** @var string */
    private $className;

    /** @var bool */
    private $invokable;

    /** @var array */
    private $extended;

    /** @var array */
    private $implementedInterfaces;

    public function __construct(
        string $className,
        bool $invokable = false,
        $extended = [],
        $implementedInterfaces = []
    ) {
        $this->className = $className;
        $this->invokable = $invokable;
        $this->extended = $extended;
        $this->implementedInterfaces = $implementedInterfaces;
    }

    public function name(): string
    {
        return $this->className;
    }

    public function isInvokable(): bool
    {
        return $this->invokable;
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function isAbstract(): bool
    {
        return false;
    }

    public function isSubclassOf(string $className): bool
    {
        return in_array($className, $this->extended);
    }

    public function isCloneable(): bool
    {
        return false;
    }

    public function implementsInterface(string $className): bool
    {
        return in_array($className, $this->implementedInterfaces);
    }

    public function annotations(): Annotations
    {
        return new Annotations([]);
    }

    public function fileName(): string
    {
        return '/path/to/my/file.php';
    }

    public function properties(): Properties
    {
        return new Properties($this->name(), []);
    }

    public function methods(): Methods
    {
        return new Methods($this->name(), []);
    }
}
