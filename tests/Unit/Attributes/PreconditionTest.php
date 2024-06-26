<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit\Attributes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use OnurSimsek\Precondition\Attributes\Precondition;
use OnurSimsek\Precondition\Exceptions\PreconditionFailedException;
use OnurSimsek\Precondition\Exceptions\PreconditionRequiredException;
use OnurSimsek\Precondition\Tests\TestCase;
use OnurSimsek\Precondition\Tests\Unit\Fixtures\LostUpdateValidator;
use OnurSimsek\Precondition\Tests\Unit\Fixtures\PrivateArticleValidator;
use PHPUnit\Framework\Attributes\Test;

class PreconditionTest extends TestCase
{
    private Precondition $precondition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->precondition = new Precondition(LostUpdateValidator::class);
    }

    #[Test]
    public function throw_precondition_required_exception()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('header')
            ->with('If-Unmodified-Since')
            ->willReturn(null);

        self::expectException(PreconditionRequiredException::class);
        $this->precondition->validate($request);
    }

    #[Test]
    public function throw_precondition_failed_exception()
    {
        $updatedAt = Date::create(2024, 1, 15, 10, 00, 00, 'utc');

        $article = new \stdClass();
        $article->updated_at = $updatedAt;

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->exactly(2))
            ->method('header')
            ->with('If-Unmodified-Since')
            ->willReturn((clone $updatedAt)->addYear()->format(DATE_RFC7231));

        $request->expects($this->once())
            ->method('route')
            ->with('article')
            ->willReturn($article);

        self::expectException(PreconditionFailedException::class);
        $this->precondition->validate($request);
    }

    #[Test]
    public function it_can_be_validate_a_proper_request()
    {
        $updatedAt = Date::create(2024, 1, 15, 10, 00, 00, 'utc');

        $article = new \stdClass();
        $article->updated_at = $updatedAt;

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->exactly(2))
            ->method('header')
            ->with('If-Unmodified-Since')
            ->willReturn($updatedAt->format(DATE_RFC7231));

        $request->expects($this->once())
            ->method('route')
            ->with('article')
            ->willReturn($article);

        self::assertTrue($this->precondition->validate($request));
    }

    #[Test]
    public function it_can_be_validate_sometimes()
    {
        $precondition = new Precondition(PrivateArticleValidator::class);

        $article = new \stdClass();
        $article->is_private = false;

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->any())
            ->method('route')
            ->with('article')
            ->willReturn($article);

        self::assertTrue($precondition->validate($request));

        // Private article required exception test
        $article->is_private = true;
        self::expectException(PreconditionRequiredException::class);
        $precondition->validate($request);

        // Private article validation failed exception test
        $request->expects($this->exactly(2))
            ->method('header')
            ->with('X-Article-Secret-Code')
            ->willReturn(1234);

        $article->is_private = true;
        $article->secret_code = 4321;

        self::expectException(PreconditionFailedException::class);
        $precondition->validate($request);

        // Private article validate test
        $secret = 1234;
        $request->expects($this->exactly(2))
            ->method('header')
            ->with('X-Article-Secret-Code')
            ->willReturn($secret);

        $article->is_private = true;
        $article->secret_code = $secret;

        self::assertTrue($precondition->validate($request));
    }
}
