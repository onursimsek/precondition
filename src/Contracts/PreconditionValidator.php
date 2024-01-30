<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Contracts;

use Illuminate\Http\Request;

interface PreconditionValidator
{
    public function parameter(Request $request);

    public function __invoke(Request $request): bool;
}
