<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Contracts;

use Illuminate\Http\Request;

interface PreconditionValidator
{
    public function when(Request $request): bool;

    public function preProcess(): void;

    public function parameter(Request $request);

    public function __invoke(Request $request): bool;

    public function getRequiredMessage(): string;

    public function getFailedMessage(): string;
}
