<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

use Ray\Aop\MethodInvocation;

class Validation implements ValidationInterface
{
    private $failure = [];

    private $invocation;

    public function __construct(array $failure = [])
    {
        $this->failure = $failure;
    }

    public function setInvocation(MethodInvocation $invocation)
    {
        $this->invocation = $invocation;
    }

    public function addError($name, $message)
    {
        $this->failure[$name][] = $message;
    }

    public function getMessages()
    {
        return $this->failure;
    }

    public function getInvocation()
    {
        return $this->invocation;
    }
}
