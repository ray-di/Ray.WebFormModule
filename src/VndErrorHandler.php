<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule;

use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Annotation\VndError;
use Ray\WebFormModule\Exception\ValidationException;
use ReflectionMethod;

final class VndErrorHandler implements FailureHandlerInterface
{
    /** {@inheritdoc} */
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form)
    {
        unset($formValidation);
        $vndError = $this->getVndErrorAttribute($invocation->getMethod());
        $error = new FormValidationError($this->makeVndError($form, $vndError));

        throw new ValidationException('Validation failed.', 400, null, $error);
    }

    private function getVndErrorAttribute(ReflectionMethod $method): VndError|null
    {
        $attributes = $method->getAttributes(VndError::class);
        if ($attributes === []) {
            return null;
        }

        $instance = $attributes[0]->newInstance();
        assert($instance instanceof VndError);

        return $instance;
    }

    private function makeVndError(AbstractForm $form, VndError $vndError = null)
    {
        $body = ['message' => 'Validation failed'];
        $body['path'] = $_SERVER['PATH_INFO'] ?? '';
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

        if ($vndError->href) {
            $body['href'] = $vndError->href;
        }

        return $body;
    }
}
