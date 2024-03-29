<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition;

use Illuminate\Support\ServiceProvider;
use OnurSimsek\Precondition\Middleware\PreconditionRequest;

use function config_path;

class PreconditionServiceProvider extends ServiceProvider
{
    private string $configPath = __DIR__ . '/../config/precondition.php';

    public function boot(): void
    {
        $this->publishes([
            $this->configPath => config_path('precondition.php'),
        ], 'precondition-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath, 'precondition');

        $this->registerMiddleware();
    }

    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('precondition', PreconditionRequest::class);
    }
}
