<?php

namespace Ray\WebFormModule;

use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;
use Aura\Input\Filter;
use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Aop\Arguments;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\WebFormModule\Exception\ValidationException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;

class AuraInputInterceptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectiveMethodInvocation
     */
    private $methodInvocation;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @param $method
     */
    public function getMethodInvocation($method, array $submit, FailureHandlerInterface $handler = null)
    {
        $handler = $handler ?: new OnFailureMethodHandler;
        $object = $this->getController($submit);
        return new ReflectiveMethodInvocation(
            $object,
            new \ReflectionMethod($object, $method),
            new Arguments([]),
            [
                new AuraInputInterceptor(new AnnotationReader, $handler)
            ]
        );
    }

    public function getController(array $submit)
    {
        $controller = new FakeController;
        $fakeForm = new FakeForm(new Builder, new Filter);
        $fakeForm->setSubmit($submit);
        $fakeForm->setFormHelper(new HelperLocatorFactory);
        $controller->setForm($fakeForm);

        return $controller;
    }

    public function proceed($controller)
    {
        $invocation = new ReflectiveMethodInvocation(
            $controller,
            new \ReflectionMethod($controller, 'createAction'),
            new Arguments([]),
            [
                new AuraInputInterceptor(new AnnotationReader, new OnFailureMethodHandler)
            ]
        );
        $invocation->proceed();
    }

    public function testProceedFailed()
    {
        $invocation = $this->getMethodInvocation('createAction', []);
        $result = $invocation->proceed();
        $this->assertSame('400', $result);
    }

    public function testProceed()
    {
        $invocation = $this->getMethodInvocation('createAction', ['name' => 'BEAR']);
        $result = $invocation->proceed();
        $this->assertSame('201', $result);
    }

    public function invalidControllerProvider()
    {
        return [
            [new FakeInvalidController1],
            [new FakeInvalidController2]
        ];
    }

    /**
     * @dataProvider invalidControllerProvider
     *
     * @param $controller
     */
    public function testInvalidFormPropertyByMissingProperty($controller)
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        $this->proceed($controller);
    }

    public function testInvalidFormPropertyException()
    {
        $this->setExpectedException(InvalidOnFailureMethod::class);
        $controller = new FakeInvalidController3;
        $controller->setForm(new FakeForm(new Builder, new Filter));
        $this->proceed($controller);
    }

    public function testInvalidFormPropertyByInvalidInstance()
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        $object = new FakeInvalidController1;
        $invocation = new ReflectiveMethodInvocation(
            $object,
            new \ReflectionMethod($object, 'createAction'),
            new Arguments([]),
            [
                new AuraInputInterceptor(new AnnotationReader, new OnFailureMethodHandler)
            ]
        );
        $invocation->proceed();
    }

    public function testProceedWithVndErrorHandler()
    {
        try {
            $invocation = $this->getMethodInvocation('createAction', [], new VndErrorHandler(new AnnotationReader));
            $invocation->proceed();
        } catch (ValidationException $e) {
            $this->assertInstanceOf(FormValidationError::class, $e->error);
            $json = (string) $e->error;
            $this->assertSame('{
    "message": "Validation failed",
    "path": "",
    "validation_messages": {
        "name": [
            "Name must be alphabetic only."
        ]
    }
}', $json);
        }
    }
}
