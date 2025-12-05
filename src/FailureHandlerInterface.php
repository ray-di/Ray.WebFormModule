<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;

interface FailureHandlerInterface
{
    /** @return mixed */
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form);
}
