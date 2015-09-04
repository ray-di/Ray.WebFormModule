<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;

final class OnFailureMethodHandler implements FailureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(FormValidation $formValidation, MethodInvocation $invocation, AbstractAuraForm $form)
    {
        unset($form);
        $args = (array) $invocation->getArguments();
        $object = $invocation->getThis();
        if (! method_exists($object, $formValidation->onFailure)) {
            throw new InvalidOnFailureMethod($formValidation->onFailure);
        }

        return call_user_func_array([$invocation->getThis(), $formValidation->onFailure], $args);
    }
}
