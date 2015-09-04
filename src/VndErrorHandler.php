<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Annotation\VndError;
use Ray\WebFormModule\Exception\ValidationException;

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
    public function handle(FormValidation $formValidation, MethodInvocation $invocation, AbstractAuraForm $form)
    {
        unset($formValidation);
        $vndError = $this->reader->getMethodAnnotation($invocation->getMethod(), VndError::class);
        $error =  new FormValidationError($this->makeVndError($form, $vndError));
        $e = new ValidationException('Validation failed.', 400, null, $error);

        throw $e;
    }

    private function makeVndError(AbstractAuraForm $form, VndError $vndError = null)
    {

        $body = ['message' => 'Validation failed'];
        $body['path'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $body['validation_messages'] = $form->getMessages();
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
