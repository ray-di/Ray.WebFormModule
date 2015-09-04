<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\FormValidation;

interface FailureHandlerInterface
{
    public function handle(FormValidation $formValidation, MethodInvocation $invocation, AbstractAuraForm $form);
}
