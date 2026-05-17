<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;

use function call_user_func_array;
use function get_class;
use function method_exists;

final class OnFailureMethodHandler implements FailureHandlerInterface
{
    public const FAILURE_SUFFIX = 'ValidationFailed';

    /**
     * {@inheritDoc}
     *
     * @param MethodInvocation<object> $invocation
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
        if (! method_exists($object, $onFailureMethod)) {
            throw new InvalidOnFailureMethod(get_class($invocation->getThis()));
        }

        /** @var callable $callable */
        $callable = [$object, $onFailureMethod];

        return call_user_func_array($callable, $args);
    }
}
