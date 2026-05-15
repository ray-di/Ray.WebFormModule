<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use function array_shift;
use Aura\Input\AntiCsrfInterface;
use function property_exists;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Inject;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\CsrfProtection;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuraInputInterceptor implements MethodInterceptor
{
    protected FailureHandlerInterface $failureHandler;

    private AntiCsrfInterface|null $antiCsrf = null;

    public function __construct(FailureHandlerInterface $handler)
    {
        $this->failureHandler = $handler;
    }

    #[Inject]
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf) : void
    {
        $this->antiCsrf = $antiCsrf;
    }

    /**
     * {@inheritdoc}
     *
     * @param MethodInvocation<object> $invocation
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
        $this->enableCsrfProtection($invocation->getMethod(), $form);
        $data = $form instanceof SubmitInterface ? $form->submit() : $this->getNamedArguments($invocation);
        /** @var array<string, mixed> $submit */
        $submit = (array) $data;
        $isValid = $this->isValid($submit, $form);
        if ($isValid === true) {
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
    }

    /**
     * @param array<string, mixed> $submit
     *
     * @throws Exception\CsrfViolationException
     */
    public function isValid(array $submit, AbstractForm $form) : bool
    {
        return $form->apply($submit);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function enableCsrfProtection(ReflectionMethod $method, AbstractForm $form) : void
    {
        if ($method->getAttributes(CsrfProtection::class) === []) {
            return;
        }

        if (! $this->antiCsrf instanceof AntiCsrfInterface) {
            throw new InvalidArgumentException('#[CsrfProtection] requires AntiCsrfInterface');
        }

        $form->enableAntiCsrf($this->antiCsrf);
    }

    private function getValidationAttribute(ReflectionMethod $method) : AbstractValidation|null
    {
        $attributes = $method->getAttributes(AbstractValidation::class, ReflectionAttribute::IS_INSTANCEOF);
        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Return arguments as named arguments.
     *
     * @param MethodInvocation<object> $invocation
     *
     * @return array<string, mixed>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getNamedArguments(MethodInvocation $invocation) : array
    {
        $submit = [];
        $params = $invocation->getMethod()->getParameters();
        $args = $invocation->getArguments()->getArrayCopy();
        foreach ($params as $param) {
            $arg = array_shift($args);
            $submit[$param->getName()] = $arg;
        }

        if (isset($_POST[AntiCsrf::TOKEN_KEY])) {
            $submit[AntiCsrf::TOKEN_KEY] = $_POST[AntiCsrf::TOKEN_KEY];
        }

        return $submit;
    }

    private function getFormProperty(AbstractValidation $formValidation, object $object) : AbstractForm
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
