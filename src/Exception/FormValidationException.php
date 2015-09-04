<?php
/**
 * This file is part of the Ray.WebFormModule
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\WebFormModule\Exception;

use Ray\WebFormModule\FormValidationError;

class FormValidationException extends \Exception
{
    public $error;

    public function __construct($message = '', $code = 0, \Exception $e = null, FormValidationError $error = null)
    {
        parent::__construct($message, $code, $e);
        $this->error = $error;
    }
}
