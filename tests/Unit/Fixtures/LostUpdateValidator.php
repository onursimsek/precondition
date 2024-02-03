<?php

namespace OnurSimsek\Precondition\Tests\Unit\Fixtures;

use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class LostUpdateValidator extends PreconditionValidator
{
    public function parameter(Request $request): array|string|null
    {
        return $request->header('If-Unmodified-Since');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->route('article')->updated_at;
    }
}
