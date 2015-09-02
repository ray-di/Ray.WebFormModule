<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\FormValidationException;

final class VndErrorHandler implements FailureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(FormValidation $formValidation, MethodInvocation $invocation, AbstractAuraForm $form)
    {
        $messages = $form->getMessages();
        $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
        $error =  new FormValidationError($path, $messages);
        $e = new FormValidationException('Validation failed.', 400, null, $error);

        throw $e;
    }
}
