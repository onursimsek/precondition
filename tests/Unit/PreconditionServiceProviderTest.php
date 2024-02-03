<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit;

use OnurSimsek\Precondition\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PreconditionServiceProviderTest extends TestCase
{
    #[Test]
    public function it_provides_to_self(): void
    {
        self::assertEquals(1, 1);
    }
}
