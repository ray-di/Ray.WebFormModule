<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Exception;

use Exception;
use Ray\WebFormModule\FormValidationError;
use Throwable;

class ValidationException extends Exception
{
    public $error;

    public function __construct($message = '', $code = 0, Throwable|null $e = null, FormValidationError|null $error = null)
    {
        parent::__construct($message, $code, $e);

        $this->error = $error;
    }
}
