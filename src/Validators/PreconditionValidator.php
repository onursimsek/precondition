<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Validators;

use Illuminate\Http\Request;
use OnurSimsek\Precondition\Contracts\PreconditionValidator as PreconditionValidatorContract;

abstract class PreconditionValidator implements PreconditionValidatorContract
{
    final public function getRequiredMessage(): string
    {
        return isset($this->messages()['required']) ? $this->messages()['required'] : config('precondition.required_message');
    }

    final public function getFailedMessage(): string
    {
        return isset($this->messages()['failed']) ? $this->messages()['failed'] : config('precondition.failed_message');
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => config('precondition.required_message'),
            'failed' => config('precondition.failed_message'),
        ];
    }

    public function preProcess(): void
    {
        //
    }

    public function when(Request $request): bool
    {
        return true;
    }
}
