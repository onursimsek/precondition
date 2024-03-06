<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use OnurSimsek\Precondition\Reflection;
use Symfony\Component\HttpFoundation\Response;

class PreconditionRequest
{
    public function __construct(private readonly Router $router, private readonly Reflection $reflection)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $currentRoute = $this->router->getCurrentRoute();
        $controllerClass = $currentRoute->getControllerClass();

        if (! $controllerClass || in_array($controllerClass, ['\Illuminate\Routing\ViewController'])) {
            return $next($request);
        }

        return $this->handleRequestUsingAttribute($request, $next, $controllerClass, $currentRoute->getActionMethod());
    }

    private function handleRequestUsingAttribute(Request $request, Closure $next, string $controller, string $action): Response
    {
        $reflection = $this->reflection->reflect($controller, $action);
        if (! $reflection->hasPreconditionAttribute()) {
            return $next($request);
        }

        $reflection->getPreconditionInstance()->validate($request);

        return $next($request);
    }
}
