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
        $onMethods = $this->findOnMethods($class, $valid);
        if ($onMethods[0] && $onMethods[0] instanceof \ReflectionMethod) {
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
     *
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
        $className = $class->implementsInterface(WeavedInterface::class) ? $class->getParentClass()->name : $class->name;
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
            $annotations = $this->reader->getMethodAnnotations($method);
            list($onValidateMethod, $onFailureMethod) = $this->scanAnnotation($valid, $annotations, $method,
                $onValidateMethod, $onFailureMethod
            );
        }

        return [$onValidateMethod, $onFailureMethod];
    }

    /**
     * @param Valid             $valid
     * @param array             $annotations
     * @param \ReflectionMethod $method
     * @param \ReflectionMethod $onValidateMethod
     * @param \ReflectionMethod $onFailureMethod
     *
     * @return array
     */
    private function scanAnnotation(
        Valid $valid,
        array $annotations,
        \ReflectionMethod $method,
        \ReflectionMethod $onValidateMethod = null,
        \ReflectionMethod $onFailureMethod = null
    ) {
        foreach ($annotations as $annotation) {
            if ($this->isOnValidateFound($annotation, $valid, $onValidateMethod)) {
                $onValidateMethod = $method;
            }
            if ($this->isOnFailureFound($annotation, $valid, $onFailureMethod)) {
                $onFailureMethod = $method;
            }
        }

        return [$onValidateMethod ,$onFailureMethod];
    }

    /**
     * @param object            $annotation
     * @param Valid             $valid
     * @param \ReflectionMethod $onValidateMethod
     *
     * @return bool
     */
    private function isOnValidateFound($annotation, Valid $valid, \ReflectionMethod $onValidateMethod = null)
    {
        return (is_null($onValidateMethod) && $annotation instanceof OnValidate && $annotation->value === $valid->value) ? true : false;
    }

    /**
     * @param object            $annotation
     * @param Valid             $valid
     * @param \ReflectionMethod $onFailureMethod
     *
     * @return bool
     */
    private function isOnFailureFound($annotation, Valid $valid, \ReflectionMethod $onFailureMethod = null)
    {
        return (is_null($onFailureMethod) && $annotation instanceof OnFailure && $annotation->value === $valid->value) ? true : false;
    }
}
