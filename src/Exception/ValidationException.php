<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Exception;

use Exception;
use Ray\WebFormModule\FormValidationError;

class ValidationException extends Exception
{
    public ?FormValidationError $error;

    public function __construct(string $message = '', int $code = 0, ?Exception $e = null, ?FormValidationError $error = null)
    {
        parent::__construct($message, $code, $e);
        $this->error = $error;
    }
}
