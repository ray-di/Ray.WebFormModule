<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Di\Di\Named;

class InputValidationInterceptor extends AuraInputInterceptor
{
    public function __construct(
        #[Named('vnd_error')]
        FailureHandlerInterface $handler,
    ) {
        parent::__construct($handler);
    }
}
