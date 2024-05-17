<?php

declare(strict_types=1);

namespace OnurSimsek\Precondition\Attributes;

use Attribute;
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Contracts\Attribute as AttributeContract;
use OnurSimsek\Precondition\Exceptions\PreconditionFailedException;
use OnurSimsek\Precondition\Exceptions\PreconditionRequiredException;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

#[Attribute(Attribute::TARGET_METHOD)]
class Precondition implements AttributeContract
{
    private PreconditionValidator $validator;

    public function __construct(string $validator)
    {
        $this->validator = new $validator();
    }

    public function validate(Request $request): bool
    {
        if (! $this->validator->when($request)) {
            return true;
        }

        if (empty($this->validator->parameter($request))) {
            $this->validator->preProcess();

            throw new PreconditionRequiredException($this->validator->getRequiredMessage());
        }

        if (! ($this->validator)($request)) {
            throw new PreconditionFailedException($this->validator->getFailedMessage());
        }

        return true;
    }
}
