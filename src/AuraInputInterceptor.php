<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

use function array_shift;
use function property_exists;

class AuraInputInterceptor implements MethodInterceptor
{
    protected FailureHandlerInterface $failureHandler;

    public function __construct(FailureHandlerInterface $handler)
    {
        $this->failureHandler = $handler;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function invoke(MethodInvocation $invocation)
    {
        $object = $invocation->getThis();
        $formValidation = $this->getValidationAttribute($invocation->getMethod());
        if ($formValidation === null) {
            throw new InvalidArgumentException('The method must be attributed with #[FormValidation] or #[InputValidation]');
        }

        $form = $this->getFormProperty($formValidation, $object);
        $data = $form instanceof SubmitInterface ? $form->submit() : $this->getNamedArguments($invocation);
        $isValid = $this->isValid($data, $form);
        if ($isValid === true) {
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
    }

    private function getValidationAttribute(ReflectionMethod $method): AbstractValidation|null
    {
        $attributes = $method->getAttributes(AbstractValidation::class, ReflectionAttribute::IS_INSTANCEOF);
        if ($attributes === []) {
            return null;
        }

        $instance = $attributes[0]->newInstance();
        assert($instance instanceof AbstractValidation);

        return $instance;
    }

    /**
     * @param array        $submit
     * @param AbstractForm $form
     *
     * @return bool
     * @throws Exception\CsrfViolationException
     *
     */
    public function isValid(array $submit, AbstractForm $form): bool
    {
        return $form->apply($submit);
    }

    /**
     * Return arguments as named arguments.
     *
     * @param MethodInvocation $invocation
     *
     * @return array
     */
    private function getNamedArguments(MethodInvocation $invocation): array
    {
        $submit = [];
        $params = $invocation->getMethod()->getParameters();
        $args = $invocation->getArguments()->getArrayCopy();
        foreach ($params as $param) {
            $arg = array_shift($args);
            $submit[$param->getName()] = $arg;
        }

        // has token?
        if (isset($_POST[AntiCsrf::TOKEN_KEY])) {
            $submit[AntiCsrf::TOKEN_KEY] = $_POST[AntiCsrf::TOKEN_KEY];
        }

        return $submit;
    }

    /**
     * Return form property
     *
     * @param AbstractValidation $formValidation
     * @param object             $object
     *
     * @return mixed
     */
    private function getFormProperty(AbstractValidation $formValidation, $object)
    {
        if (! property_exists($object, $formValidation->form)) {
            throw new InvalidFormPropertyException($formValidation->form);
        }

        $prop = (new ReflectionClass($object))->getProperty($formValidation->form);
        $prop->setAccessible(true);
        $form = $prop->getValue($object);
        if (! $form instanceof AbstractForm) {
            throw new InvalidFormPropertyException($formValidation->form);
        }

        return $form;
    }
}
