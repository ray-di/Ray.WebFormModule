<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\WebFormModule;

use Aura\Input\Form;
use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;

class AuraInputInterceptor implements MethodInterceptor
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var FailureHandlerInterface
     */
    private $failureHandler;

    /**
     * @param Reader $reader Annotation reader
     */
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
        $formValidation = $this->reader->getMethodAnnotation($invocation->getMethod(), FormValidation::class);
        $form = $this->getFormProperty($formValidation, $object);
        $data = $object instanceof SubmitInterface ? $object->submit() : $this->getNamedArguments($invocation);
        $isValid = $this->isValid($data, $form);
        if ($isValid === true) {
            // validation   success
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
    }

    /**
     * Return arguments as named argumentes.
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

        return $submit;
    }

    /**
     * @param array $submit
     * @param Form  $form
     *
     * @return bool
     *
     * @throws \Aura\Input\Exception\CsrfViolation
     */
    public function isValid(array $submit, AbstractForm $form)
    {
        $isValid = $form->apply($submit);

        return $isValid;
    }

    /**
     * Return form property
     *
     * @param FormValidation $formValidation
     * @param object         $object
     *
     * @return AbstractForm
     */
    private function getFormProperty(FormValidation $formValidation, $object)
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
