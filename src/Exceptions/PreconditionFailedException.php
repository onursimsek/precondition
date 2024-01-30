<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreconditionFailedException extends PreconditionException
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], Response::HTTP_PRECONDITION_FAILED);
    }
}
