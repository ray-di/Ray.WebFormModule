<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\VndError;
use Ray\WebFormModule\Exception\ValidationException;
use ReflectionMethod;

final class VndErrorHandler implements FailureHandlerInterface
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
     */
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form)
    {
        unset($formValidation);
        $vndError = $this->getVndErrorAnnotation($invocation->getMethod());
        $error = new FormValidationError($this->makeVndError($form, $vndError));

        throw new ValidationException('Validation failed.', 400, null, $error);
    }

    /**
     * Get VndError annotation from PHP 8 attributes or Doctrine annotations
     */
    private function getVndErrorAnnotation(ReflectionMethod $method): ?VndError
    {
        // Try PHP 8 attributes first
        $attributes = $method->getAttributes(VndError::class);
        if (! empty($attributes)) {
            return $attributes[0]->newInstance();
        }

        // Fall back to Doctrine annotations
        return $this->reader->getMethodAnnotation($method, VndError::class);
    }

    private function makeVndError(AbstractForm $form, VndError $vndError = null)
    {
        $body = ['message' => 'Validation failed'];
        $body['path'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $body['validation_messages'] = $form->getFailureMessages();
        $body = $vndError ? $this->optionalAttribute($vndError) + $body : $body;

        return $body;
    }

    private function optionalAttribute(VndError $vndError)
    {
        $body = [];
        if ($vndError->message) {
            $body['message'] = $vndError->message;
        }
        if ($vndError->path) {
            $body['path'] = $vndError->path;
        }
        if ($vndError->logref) {
            $body['logref'] = $vndError->logref;
        }

        return $body;
    }
}
