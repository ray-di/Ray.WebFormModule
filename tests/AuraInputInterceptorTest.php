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

    protected function setUp(): void
    {
        $this->injector = new Injector(new class () extends AbstractModule {
            protected function configure()
            {
                $this->install(new AuraInputModule());
                $this->bind(FormInterface::class)->annotatedWith('contact_form')->to(FakeForm::class);
            }
        });
        $this->controller = $this->injector->getInstance(FakeController::class);
    }

//    /**
//     * @param $method
//     */
//    public function getMethodInvocation(string $method, array $submit, FailureHandlerInterface $handler = null)
//    {
//        $handler = $handler ?: new OnFailureMethodHandler;
//        $object = $this->getController($submit);
//
//        $invocation =  new ReflectiveMethodInvocation(
//            $object,
//            $method,
//            $submit,
//            [
//                new AuraInputInterceptor(new AnnotationReader, $handler)
//            ]
//        );
//
//        return $invocation;
//    }
//
//    public function getController(array $submit)
//    {
//        $controller = new FakeController;
//        /** @var $fakeForm FakeForm */
//        $fakeForm = (new FormFactory)->newInstance(FakeForm::class);
//        $fakeForm->setSubmit($submit);
//        $controller->setForm($fakeForm);
//
//        return $controller;
//    }
//
//    public function proceed($controller)
//    {
//        $invocation = new ReflectiveMethodInvocation(
//            $controller,
//            new \ReflectionMethod($controller, 'createAction'),
//            [],
//            [
//                new AuraInputInterceptor(new AnnotationReader, new OnFailureMethodHandler)
//            ]
//        );
//        $invocation->proceed();
//    }

    /** Test proceed failed. */
    public function testProceedFailed(): void
    {
        $result = $this->controller->createAction([]);
        $this->assertSame('400', $result);
    }

    public function testProceed(): void
    {
        $result = $this->controller->createAction('BEAR');
        $this->assertSame('201', $result);
    }

    /** @return array<array<FakeInvalidController1|FakeInvalidController2>> */
    public function invalidControllerProvider(): array
    {
        return [
            [$this->injector->getInstance(FakeInvalidController1::class)],
            [$this->injector->getInstance(FakeInvalidController2::class)],
        ];
    }

    public function testInvalidFormPropertyByMissingProperty(): void
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyByMissingProperty2(): void
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController2::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyException(): void
    {
        $this->expectException(InvalidFormPropertyException::class);
        /** @var FakeInvalidController3 $controller */
        $controller = $this->injector->getInstance(FakeInvalidController3::class);
        $controller->createAction('');
    }

    public function testInvalidFormPropertyByInvalidInstance(): void
    {
        $this->expectException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction();
    }

    public function testProceedWithVndErrorHandler(): void
    {
        $injector = new Injector(new FakeVndErrorModule());
        /** @var FakeController $controller */
        $controller = $injector->getInstance(FakeController::class);
        try {
            $controller->createAction('');
            $this->fail('ValidationException expected');
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
