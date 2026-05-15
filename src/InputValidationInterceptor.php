<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Ray\Di\Di\Named;

final class InputValidationInterceptor extends AuraInputInterceptor
{
    public function __construct(#[Named('vnd_error')]
    FailureHandlerInterface $handler,)
    {
        parent::__construct($handler);
    }
}
