<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit;

use OnurSimsek\Precondition\Attributes\Precondition;
use OnurSimsek\Precondition\Reflection;
use OnurSimsek\Precondition\Tests\TestCase;
use OnurSimsek\Precondition\Tests\Unit\Fixtures\Controller;
use PHPUnit\Framework\Attributes\Test;

class ReflectionTest extends TestCase
{
    private Reflection $reflection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reflection = new Reflection();
    }

    #[Test]
    public function it_can_be_reflect_a_method()
    {
        $actual = $this->reflection->reflect(Controller::class, 'withPreconditionMethod');

        self::assertInstanceOf(Reflection::class, $actual);
    }

    #[Test]
    public function it_can_be_found_the_attribute()
    {
        $reflection = $this->reflection->reflect(Controller::class, 'withPreconditionMethod');
        self::assertTrue($reflection->hasPreconditionAttribute());

        $reflection = $this->reflection->reflect(Controller::class, 'withoutPreconditionMethod');
        self::assertFalse($reflection->hasPreconditionAttribute());
    }

    #[Test]
    public function it_can_be_create_the_attribute_instance()
    {
        $reflection = $this->reflection->reflect(Controller::class, 'withPreconditionMethod');

        self::assertInstanceOf(Precondition::class, $reflection->getPreconditionInstance());
    }
}
