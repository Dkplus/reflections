<?php
namespace spec\Dkplus\Reflections\Mock;

final class ClassReflectionStubBuilder
{
    /** @var string */
    private $className = 'AClassNameThatNoOneWouldUse';

    /** @var string[] */
    private $implementedInterfaces = [];

    /** @var string[] */
    private $extendedClasses = [];

    /** @var bool */
    private $invokable = false;

    public static function build()
    {
        return new self();
    }

    public function withClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    public function withInvokable(bool $invokable): self
    {
        $this->invokable = $invokable;
        return $this;
    }

    public function implement(string $className): self
    {
        $this->implementedInterfaces[] = $className;
        return $this;
    }

    public function extend(string $className): self
    {
        $this->extendedClasses[] = $className;
        return $this;
    }

    public function finish(): ClassReflectionStub
    {
        return new ClassReflectionStub(
            $this->className,
            $this->invokable,
            $this->extendedClasses,
            $this->implementedInterfaces
        );
    }
}
