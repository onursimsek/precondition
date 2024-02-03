<?php

namespace OnurSimsek\Precondition\Tests\Unit\Fixtures;

use Illuminate\Http\JsonResponse;
use OnurSimsek\Precondition\Attributes\Precondition;

class Controller
{
    #[Precondition(LostUpdateValidator::class)]
    public function withPreconditionMethod(): JsonResponse
    {
        return response()->json();
    }

    public function withoutPreconditionMethod(): JsonResponse
    {
        return response()->json();
    }
}
