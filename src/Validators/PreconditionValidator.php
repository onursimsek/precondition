<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Validators;

use OnurSimsek\Precondition\Contracts\PreconditionValidator as PreconditionValidatorContract;

abstract class PreconditionValidator implements PreconditionValidatorContract
{
    final public function getRequiredMessage()
    {
        return isset($this->messages()['required']) ? $this->messages()['required'] : config('precondition.required_message');
    }

    final public function getFailedMessage()
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
}
