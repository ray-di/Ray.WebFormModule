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
    /**
     * @var array [$name =>]
     */
    private $failure = [];

    /**
     * @var MethodInvocation
     */
    private $invocation;

    public function __construct(array $failure = [])
    {
        $this->failure = $failure;
    }

    public function setInvocation(MethodInvocation $invocation)
    {
        $this->invocation = $invocation;
    }

    /**
     * @param string $name    error target name
     * @param string $message message
     */
    public function addError($name, $message)
    {
        $this->failure[$name][] = $message;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->failure;
    }

    /**
     * @return MethodInvocation
     */
    public function getInvocation()
    {
        return $this->invocation;
    }
}
