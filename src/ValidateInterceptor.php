<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\WeavedInterface;
use Ray\Validation\Annotation\OnFailure;
use Ray\Validation\Annotation\OnValidate;
use Ray\Validation\Annotation\Valid;
use Ray\Validation\Exception\InvalidArgumentException;
use Ray\Validation\Exception\ValidateMethodNotFound;

class ValidateInterceptor implements MethodInterceptor
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function invoke(MethodInvocation $invocation)
    {
        list($onValidate, $onFailure) = $this->getOnValidate($invocation->getMethod());
        list($isValid, $failure) = $this->validate($invocation, $onValidate, $onFailure);
        if ($isValid === true) {
            // validation success
            return $invocation->proceed();
        }

        // onFailure
        return $failure;
    }

    /**
     * Return Validate and OnFailure method
     *
     * @param \ReflectionMethod $method
     *
     * @return \ReflectionMethod[] [$onValidateMethod, $onFailureMethod]
     */
    private function getOnValidate(\ReflectionMethod $method)
    {
        /** @var $valid Valid */
        $valid = $this->reader->getMethodAnnotation($method, Valid::class);
        $class = $method->getDeclaringClass();
        $onFailureMethod = null;
        $onMethods = $this->findOnMethods($class, $valid);
        if ($onMethods[0]) {
            return $onMethods;
        }
        throw new ValidateMethodNotFound($method->getShortName());
    }

    /**
     * Validate with Validate method
     *
     * @param MethodInvocation  $invocation
     * @param \ReflectionMethod $onValidate
     * @param \ReflectionMethod $onFailure
     *
     * @return bool|mixed|InvalidArgumentException
     * @throws \Exception
     */
    private function validate(MethodInvocation $invocation, \ReflectionMethod $onValidate, \ReflectionMethod $onFailure = null)
    {
        $validation = $onValidate->invokeArgs($invocation->getThis(), (array) $invocation->getArguments());
        if ($validation instanceof Validation && $validation->getMessages()) {
            /* @var $validation Validation */
            $validation->setInvocation($invocation);
            $failure = $this->getFailure($invocation, $validation, $onFailure);
            if ($failure instanceof \Exception) {
                throw $failure;
            }

            return [false, $failure];
        }

        return [true, null];
    }

    /**
     * Return result OnFailure
     *
     * @param MethodInvocation  $invocation
     * @param FailureInterface  $failure
     * @param \ReflectionMethod $onFailure
     *
     * @return mixed|InvalidArgumentException
     */
    private function getFailure(MethodInvocation $invocation, FailureInterface $failure, \ReflectionMethod $onFailure = null)
    {
        if ($onFailure) {
            return $onFailure->invoke($invocation->getThis(), $failure);
        }

        return $this->failureException($invocation, $failure);
    }

    /**
     * Return InvalidArgumentException exception
     *
     * @param MethodInvocation $invocation
     * @param FailureInterface $failure
     *
     * @return InvalidArgumentException
     */
    private function failureException(MethodInvocation $invocation, FailureInterface $failure)
    {
        $class = new \ReflectionClass($invocation->getThis());
        $className = $class->implementsInterface(WeavedInterface::class) ? $class->getParentClass()->getName() : $class->getName();
        $errors = json_encode($failure->getMessages());
        $msg = sprintf("%s::%s() %s", $className, $invocation->getMethod()->name, $errors);

        return new InvalidArgumentException($msg, 400);
    }

    /**
     * @param \ReflectionClass $class
     * @param Valid            $valid
     *
     * @return \ReflectionMethod[]
     */
    private function findOnMethods(\ReflectionClass $class, Valid $valid)
    {
        $onValidateMethod = $onFailureMethod = null;
        foreach ($class->getMethods() as $method) {
            /* @var $onValidate OnValidate */
            $onValidate = $this->reader->getMethodAnnotation($method, OnValidate::class);
            if ($onValidate && $onValidate->value === $valid->value) {
                $onValidateMethod = $method;
            }
            $onFailure = $this->reader->getMethodAnnotation($method, OnFailure::class);
            if ($onFailure && $onFailure->value === $valid->value) {
                $onFailureMethod = $method;
            }
        }

        return [$onValidateMethod, $onFailureMethod];
    }
}
