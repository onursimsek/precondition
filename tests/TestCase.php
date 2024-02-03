<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use OnurSimsek\Precondition\PreconditionServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use WithFaker;

    protected function getPackageProviders($app): array
    {
        return [PreconditionServiceProvider::class];
    }
}
