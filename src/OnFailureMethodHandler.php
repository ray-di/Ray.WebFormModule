<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use function call_user_func_array;
use function method_exists;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;

final class OnFailureMethodHandler implements FailureHandlerInterface
{
    public const FAILURE_SUFFIX = 'ValidationFailed';

    /**
     * {@inheritdoc}
     *
     * @param AbstractValidation       $formValidation
     * @param MethodInvocation<object> $invocation
     * @param AbstractForm             $form
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
