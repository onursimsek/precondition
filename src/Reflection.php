<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition;

use OnurSimsek\Precondition\Attributes\Precondition;
use OnurSimsek\Precondition\Contracts\Attribute;
use ReflectionAttribute;
use ReflectionMethod;

class Reflection extends ReflectionMethod
{
    /** @var ReflectionAttribute[] */
    private array $attributes;

    public function __construct(string $controller, string $action)
    {
        parent::__construct($controller, $action);
    }

    private function getPreconditionAttribute(): array
    {
        return $this->attributes ?? $this->attributes = $this->getAttributes(Precondition::class);
    }

    public function hasPreconditionAttribute(): bool
    {
        return (bool)$this->getPreconditionAttribute();
    }

    public function getPreconditionInstance(): Attribute
    {
        return $this->getPreconditionAttribute()[0]->newInstance();
    }
}
