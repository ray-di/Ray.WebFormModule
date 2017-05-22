<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;

final class OnFailureMethodHandler implements FailureHandlerInterface
{
    const FAILURE_SUFFIX = 'ValidationFailed';

    /**
     * {@inheritdoc}
     */
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form)
    {
        unset($form);
        $args = (array) $invocation->getArguments();
        $object = $invocation->getThis();
        if (! $formValidation instanceof FormValidation) {
            throw new InvalidOnFailureMethod(get_class($invocation->getThis()));
        }
        $onFailureMethod = $formValidation->onFailure ?: $invocation->getMethod()->getName() . self::FAILURE_SUFFIX;
        if (! $formValidation instanceof FormValidation || ! method_exists($object, $onFailureMethod)) {
            throw new InvalidOnFailureMethod(get_class($invocation->getThis()));
        }

        return call_user_func_array([$invocation->getThis(), $onFailureMethod], $args);
    }
}
