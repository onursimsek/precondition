<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition;

use OnurSimsek\Precondition\Attributes\Precondition;
use OnurSimsek\Precondition\Contracts\Attribute;
use ReflectionAttribute;
use ReflectionMethod;

class Reflection
{
    private ReflectionMethod $reflectionMethod;
    private ReflectionAttribute $attribute;

    public function reflect(string $controller, string $action): static
    {
        $this->reflectionMethod = new ReflectionMethod($controller, $action);
        unset($this->attribute);

        return $this;
    }

    private function getPreconditionAttribute(): ?ReflectionAttribute
    {
        if (! $attributes = $this->reflectionMethod->getAttributes(Precondition::class)) {
            return null;
        }

        return $this->attribute ??= $attributes[0];
    }

    public function hasPreconditionAttribute(): bool
    {
        return (bool)$this->getPreconditionAttribute();
    }

    public function getPreconditionInstance(): Attribute
    {
        return $this->getPreconditionAttribute()->newInstance();
    }
}
