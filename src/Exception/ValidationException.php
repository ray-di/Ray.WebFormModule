<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Exception;

use Exception;
use Ray\WebFormModule\FormValidationError;
use Throwable;

class ValidationException extends Exception
{
    /** @var FormValidationError|null */
    public $error;

    /** @param string $message */
    public function __construct($message = '', int $code = 0, Throwable|null $e = null, FormValidationError|null $error = null)
    {
        parent::__construct($message, $code, $e);

        $this->error = $error;
    }
}
