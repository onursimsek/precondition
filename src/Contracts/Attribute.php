<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Contracts;

use Illuminate\Http\Request;

interface Attribute
{
    public function __construct(string $validator);

    public function validate(Request $request): bool;
}
