<?php

namespace Ray\WebFormModule;

use Ray\Di\Injector;
use Ray\WebFormModule\Exception\FormValidationException;

class VndErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FakeController
     */
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = (new Injector(new FakeVndErrorModule))->getInstance(FakeController::class);
    }

    public function testFormValidationException()
    {
        $this->setExpectedException(FormValidationException::class);
        $this->controller->createAction();
    }

    public function testFormValidationExceptionError()
    {
        try {
            $this->controller->createAction();
        } catch(FormValidationException $e) {
            $vndError = (string) $e->error;
            $this->assertSame('{
    "message": "Validation failed",
    "path": "",
    "validation_messages": {
        "name": [
            "Name must be alphabetic only."
        ]
    }
}', $vndError);
        }
    }

    public function testVndErrorAnnotation()
    {
        /** @var $controller FakeControllerVndError */
        $controller = (new Injector(new FakeVndErrorModule))->getInstance(FakeControllerVndError::class);
        try {
            $controller->createAction();
        } catch(FormValidationException $e) {
            $vndError = (string) $e->error;
            $this->assertSame('{
    "message": "foo validation failed",
    "path": "/path/to/error",
    "logref": "a1000",
    "validation_messages": {
        "name": [
            "Name must be alphabetic only."
        ]
    }
}', $vndError);
        }
    }

}
