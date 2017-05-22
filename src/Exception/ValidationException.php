<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Exception;

use Ray\WebFormModule\FormValidationError;

class ValidationException extends \Exception
{
    public $error;

    public function __construct($message = '', $code = 0, \Exception $e = null, FormValidationError $error = null)
    {
        parent::__construct($message, $code, $e);
        $this->error = $error;
    }
}
