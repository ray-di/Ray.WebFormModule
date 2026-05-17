<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Di\InjectorInterface;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use Ray\WebFormModule\Exception\ValidationException;

class AuraInputInterceptorTest extends TestCase
{
    /** @var InjectorInterface */
    private $injector;

    /** @var FakeController */
    private $controller;

    public function setUp(): void
    {
        $this->injector = new Injector(new class () extends AbstractModule {
            protected function configure()
            {
                $this->install(new AuraInputModule());
                $this->bind(FormInterface::class)->annotatedWith('contact_form')->to(FakeForm::class);
                $this->bind(FormInterface::class)->annotatedWith('mini_form')->to(FakeMiniForm::class);
            }
        });
        $this->controller = $this->injector->getInstance(FakeController::class);
    }

    public function testProceedFailed()
    {
        $result = $this->controller->createAction([]);
        $this->assertSame('400', $result);
    }

    public function testProceed()
    {
        $result = $this->controller->createAction('BEAR');
        $this->assertSame('201', $result);
    }

    public function testCsrfProtectionAttributeEnablesAntiCsrf()
    {
        /** @var FakeCsrfController $controller */
        $controller = $this->injector->getInstance(FakeCsrfController::class);
        $this->assertStringNotContainsString(AntiCsrf::TOKEN_KEY, $controller->formHtml());

        $result = $controller->createAction('BEAR');

        $this->assertSame('201', $result);
        $this->assertStringContainsString(AntiCsrf::TOKEN_KEY, $controller->formHtml());
    }

    public function testInvalidFormPropertyByMissingProperty()
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyByMissingProperty2()
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController2::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyException()
    {
        $this->expectException(InvalidFormPropertyException::class);
        /** @var FakeInvalidController3 $controller */
        $controller = $this->injector->getInstance(FakeInvalidController3::class);
        $controller->createAction('');
    }

    public function testInvalidFormPropertyByInvalidInstance()
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction('');
    }

    public function testProceedWithVndErrorHandler()
    {
        /** @var FakeControllerVndError $controller */
        $controller = $this->injector->getInstance(FakeControllerVndError::class);
        try {
            $controller->createAction('');
            $this->fail('ValidationException should be thrown');
        } catch (ValidationException $e) {
            $this->assertInstanceOf(FormValidationError::class, $e->error);
            $json = (string) $e->error;
            $this->assertSame('{
    "message": "foo validation failed",
    "path": "/path/to/error",
    "logref": "a1000",
    "href": {
        "_self": "/path/to/error",
        "help": "/path/to/help"
    },
    "validation_messages": {
        "name": [
            "Name must be alphabetic only."
        ]
    }
}', $json);
        }
    }
}
