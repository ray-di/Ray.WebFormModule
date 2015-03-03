<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

class ValidationResult implements FailureInterface
{
    private $failure = [];

    public function __construct(array $failure = [])
    {
        $this->failure = $failure;
    }

    public function addError($name, $message)
    {
        $this->failure[$name][] = $message;
    }

    public function getMessages()
    {
        return $this->failure;
    }
}
