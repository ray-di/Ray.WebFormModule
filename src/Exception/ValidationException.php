<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Exception;

use Exception;
use Ray\WebFormModule\FormValidationError;
use Throwable;

class ValidationException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable|null $e = null, public FormValidationError|null $error = null)
    {
        parent::__construct($message, $code, $e);
    }
}
