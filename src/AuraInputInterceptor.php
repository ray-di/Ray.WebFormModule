<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use ReflectionMethod;

class AuraInputInterceptor implements MethodInterceptor
{
    /**
     * @var FailureHandlerInterface
     */
    protected $failureHandler;

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
        /* @var $formValidation FormValidation */
        $method = $invocation->getMethod();
        $formValidation = $this->getValidationAttribute($method);
        $form = $this->getFormProperty($formValidation, $object);
        $data = $form instanceof SubmitInterface ? $form->submit() : $this->getNamedArguments($invocation);
        $isValid = $this->isValid($data, $form);
        if ($isValid === true) {
            // validation   success
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
    }

    /**
     * @param array        $submit
     * @param AbstractForm $form
     *
     * @throws Exception\CsrfViolationException
     *
     * @return bool
     */
    public function isValid(array $submit, AbstractForm $form)
    {
        $isValid = $form->apply($submit);

        return $isValid;
    }

    /**
     * Return arguments as named arguments.
     *
     * @param MethodInvocation $invocation
     *
     * @return array
     */
    private function getNamedArguments(MethodInvocation $invocation)
    {
        $submit = [];
        $params = $invocation->getMethod()->getParameters();
        $args = $invocation->getArguments()->getArrayCopy();
        foreach ($params as $param) {
            $arg = array_shift($args);
            $submit[$param->getName()] = $arg;
        }
        // has token ?
        if (isset($_POST[AntiCsrf::TOKEN_KEY])) {
            $submit[AntiCsrf::TOKEN_KEY] = $_POST[AntiCsrf::TOKEN_KEY];
        }

        return $submit;
    }

    /**
     * Get validation attribute from PHP 8 attributes
     */
    private function getValidationAttribute(ReflectionMethod $method): AbstractValidation
    {
        $attributes = $method->getAttributes(AbstractValidation::class, \ReflectionAttribute::IS_INSTANCEOF);
        if (empty($attributes)) {
            throw new \LogicException('FormValidation or InputValidation attribute is required');
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Return form property
     *
     * @param AbstractValidation $formValidation
     * @param object             $object
     *
     * @return AbstractForm
     */
    private function getFormProperty(AbstractValidation $formValidation, $object)
    {
        if (! property_exists($object, $formValidation->form)) {
            throw new InvalidFormPropertyException($formValidation->form);
        }
        $prop = (new \ReflectionClass($object))->getProperty($formValidation->form);
        $prop->setAccessible(true);
        $form = $prop->getValue($object);
        if (! $form instanceof AbstractForm) {
            throw new InvalidFormPropertyException($formValidation->form);
        }

        return $form;
    }
}
