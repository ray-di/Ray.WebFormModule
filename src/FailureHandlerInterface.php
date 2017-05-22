<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;

interface FailureHandlerInterface
{
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form);
}
