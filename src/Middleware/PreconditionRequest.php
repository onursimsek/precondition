<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use OnurSimsek\Precondition\Reflection;
use Symfony\Component\HttpFoundation\Response;

class PreconditionRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = Route::getRoutes()->match($request);
        if (! $route->getControllerClass()) {
            return $next($request);
        }

        return $this->handleRequestUsingAttribute($request, $next, $route->getControllerClass(), $route->getActionMethod());
    }

    private function handleRequestUsingAttribute(Request $request, Closure $next, string $controller, string $action): Response
    {
        $reflection = new Reflection($controller, $action);
        if (! $reflection->hasPreconditionAttribute()) {
            return $next($request);
        }

        $reflection->getPreconditionInstance()->validate($request);

        return $next($request);
    }
}
