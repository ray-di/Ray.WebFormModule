<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use ReflectionClass;

use function array_shift;
use function property_exists;

class AuraInputInterceptor implements MethodInterceptor
{
    protected Reader $reader;

    protected FailureHandlerInterface $failureHandler;

    public function __construct(Reader $reader, FailureHandlerInterface $handler)
    {
        $this->reader = $reader;
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
        /* @var $formValidation FormValidation */
        $method = $invocation->getMethod();
        $formValidation = $this->reader->getMethodAnnotation($method, AbstractValidation::class);
        $form = $this->getFormProperty($formValidation, $object);
        $data = $form instanceof SubmitInterface ? $form->submit() : $this->getNamedArguments($invocation);
        $isValid = $this->isValid($data, $form);
        if ($isValid === true) {
            // validation success
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
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
