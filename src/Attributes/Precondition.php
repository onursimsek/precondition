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
    public function __construct(private readonly string $validator)
    {
    }

    public function validate(Request $request): bool
    {
        /** @var PreconditionValidator $validator */
        $validator = new $this->validator();
        if (empty($validator->parameter($request))) {
            $validator->preProcess();

            throw new PreconditionRequiredException($validator->getRequiredMessage());
        }

        if (! $validator($request)) {
            throw new PreconditionFailedException($validator->getFailedMessage());
        }

        return true;
    }
}
