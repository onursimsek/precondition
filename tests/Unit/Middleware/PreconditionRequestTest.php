<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use OnurSimsek\Precondition\Attributes\Precondition;
use OnurSimsek\Precondition\Middleware\PreconditionRequest;
use OnurSimsek\Precondition\Reflection;
use OnurSimsek\Precondition\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PreconditionRequestTest extends TestCase
{
    #[Test]
    public function it_should_not_be_touch_closure_route()
    {
        $route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
        $route->expects($this->once())
            ->method('getControllerClass')
            ->willReturn(null);

        $router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
        $router->expects($this->once())
            ->method('getCurrentRoute')
            ->willReturn($route);

        $reflection = $this->getReflectionMock();
        $middleware = new PreconditionRequest($router, $reflection);

        self::assertInstanceOf(
            Response::class,
            $middleware->handle(new Request(), fn (Request $request) => new Response())
        );
    }

    #[Test]
    public function it_should_not_be_touch_when_the_action_doesnt_have_the_attribute()
    {
        $route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
        $route->expects($this->once())
            ->method('getControllerClass')
            ->willReturn('UserController');

        $route->expects($this->once())
            ->method('getActionMethod')
            ->willReturn('index');

        $router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
        $router->expects($this->once())
            ->method('getCurrentRoute')
            ->willReturn($route);

        $reflection = $this->getReflectionMock();
        $reflection->expects($this->once())
            ->method('reflect')
            ->willReturnSelf();

        $reflection->expects($this->once())
            ->method('hasPreconditionAttribute')
            ->willReturn(false);

        $middleware = new PreconditionRequest($router, $reflection);

        self::assertInstanceOf(
            Response::class,
            $middleware->handle(new Request(), fn (Request $request) => new Response())
        );
    }

    #[Test]
    public function it_can_be_validate_precondition_request()
    {
        $route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
        $route->expects($this->once())
            ->method('getControllerClass')
            ->willReturn('UserController');

        $route->expects($this->once())
            ->method('getActionMethod')
            ->willReturn('index');

        $router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
        $router->expects($this->once())
            ->method('getCurrentRoute')
            ->willReturn($route);

        $precondition = $this->getPreconditionAttributeMock();
        $precondition->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $reflection = $this->getReflectionMock();
        $reflection->expects($this->once())
            ->method('reflect')
            ->willReturnSelf();

        $reflection->expects($this->once())
            ->method('hasPreconditionAttribute')
            ->willReturn(true);

        $reflection->expects($this->once())
            ->method('getPreconditionInstance')
            ->willReturn($precondition);

        $middleware = new PreconditionRequest($router, $reflection);

        self::assertInstanceOf(
            Response::class,
            $middleware->handle(new Request(), fn (Request $request) => new Response())
        );
    }

    private function getReflectionMock(): \PHPUnit\Framework\MockObject\MockObject|Reflection
    {
        return $this->getMockBuilder(Reflection::class)->disableOriginalConstructor()->getMock();
    }

    private function getPreconditionAttributeMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(Precondition::class)->disableOriginalConstructor()->getMock();
    }
}
