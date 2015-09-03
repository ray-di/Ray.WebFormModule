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
    "path": "/",
    "message": "Validation failed",
    "validation_messages": [
        {
            "name": [
                "Name must be alphabetic only."
            ]
        }
    ]
}', $vndError);
        }
    }
}
