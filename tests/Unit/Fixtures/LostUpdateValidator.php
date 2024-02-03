<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit\Fixtures;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class LostUpdateValidator extends PreconditionValidator
{
    public function parameter(Request $request)
    {
        return $request->header('If-Unmodified-Since');
    }

    public function __invoke(Request $request): bool
    {
        return Date::createFromFormat(DATE_RFC7231, $this->parameter($request)) == $request->route('article')->updated_at;
    }
}
