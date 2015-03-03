<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\WeavedInterface;
use Ray\Validation\Exception\InvalidArgumentException;
use Ray\Validation\Exception\ValidateMethodNotFound;

class ValidateInterceptor implements MethodInterceptor
{
    const VALIDATE_PREFIX = 'onValidate';

    const INVALID_PREFIX = 'onInvalid';

    /**
     * {@inheritdoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        $method = $invocation->getMethod()->name;
        $object = $invocation->getThis();
        $target = [$object, $method];
        $onValidate = [$object, self::VALIDATE_PREFIX . ucfirst($method)];
        if (is_callable($onValidate)) {
            $validationResult = $this->validate($target, $onValidate, (array) $invocation->getArguments());
            if ($validationResult === true) {
                return $invocation->proceed();
            }

            return $validationResult;
        }

        throw new ValidateMethodNotFound($method);
    }

    private function validate(callable $target, callable $onValidate, $params)
    {
        $result = call_user_func_array($onValidate, $params);
        if ($result instanceof ValidationResult && $result->getMessages()) {
            $failure = $this->getFailure($target, $result);
            if ($failure instanceof \Exception) {
                throw $failure;
            }

            return $failure;
        }

        return true;
    }

    private function getFailure(callable $target, FailureInterface $failure)
    {
        $onInvalidate = [$target[0], self::INVALID_PREFIX . ucwords($target[1])];
        if (is_callable($onInvalidate)) {
            return call_user_func($onInvalidate, $failure);
        }

        return $this->failureException($target, $failure);
    }

    private function failureException(callable $target, FailureInterface $failure)
    {
        $class = new \ReflectionClass($target[0]);
        $class = $class->implementsInterface(WeavedInterface::class) ? $class->getParentClass() : $class;
        $errors = json_encode($failure->getMessages());
        $msg = sprintf("%s::%s() %s", $class->getName(), $target[1], $errors);

        return new InvalidArgumentException($msg, 400);
    }
}
