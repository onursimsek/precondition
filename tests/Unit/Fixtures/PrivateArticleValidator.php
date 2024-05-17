<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Tests\Unit\Fixtures;

use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class PrivateArticleValidator extends PreconditionValidator
{
    public function when(Request $request): bool
    {
        return $request->route('article')->is_private;
    }

    public function parameter(Request $request)
    {
        return $request->header('X-Article-Secret-Code');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->route('article')->secret_code;
    }
}
