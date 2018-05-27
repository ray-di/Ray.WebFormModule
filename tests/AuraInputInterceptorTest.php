<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Di\InjectorInterface;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use Ray\WebFormModule\Exception\ValidationException;

class AuraInputInterceptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InjectorInterface
     */
    private $injector;

    /**
     * @var FakeController
     */
    private $controller;

    public function setUp()
    {
        $this->injector = new Injector(new class() extends AbstractModule {
            protected function configure()
            {
                $this->install(new AuraInputModule);
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

    public function invalidControllerProvider()
    {
        return [
            [$this->injector->getInstance(FakeInvalidController1::class)],
            [$this->injector->getInstance(FakeInvalidController2::class)]
        ];
    }

    public function testInvalidFormPropertyByMissingProperty()
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyByMissingProperty2()
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController2::class);
        $controller->createAction();
    }

    public function testInvalidFormPropertyException()
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        /** @var FakeInvalidController3 $controller */
        $controller = $this->injector->getInstance(FakeInvalidController3::class);
        $controller->createAction('');
    }

    public function testInvalidFormPropertyByInvalidInstance()
    {
        $this->setExpectedException(InvalidFormPropertyException::class);
        $this->setExpectedException(InvalidFormPropertyException::class);
        $controller = $this->injector->getInstance(FakeInvalidController1::class);
        $controller->createAction('');
    }

    public function testProceedWithVndErrorHandler()
    {
        /** @var FakeController $controller */
        $controller = $this->injector->getInstance(FakeController::class);
        try {
            $controller->createAction('');
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
