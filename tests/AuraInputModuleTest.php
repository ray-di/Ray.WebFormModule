<?php

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;
use Ray\Aop\WeavedInterface;
use Ray\Di\Injector;
use Ray\WebFormModule\Exception\ValidationException;

class AuraInputModuleTest extends TestCase
{
    public function testAuraInputModule()
    {
        $injector = new Injector(new FakeModule, __DIR__ . '/tmp');
        $controller = $injector->getInstance(FakeController::class);
        $this->assertInstanceOf(WeavedInterface::class, $controller);
    }

    public function testExceptionOnFailure()
    {
        $this->expectException(ValidationException::class);
        $injector = new Injector(new FakeModule, __DIR__ . '/tmp');
        /** @var $controller FakeInputValidationController */
        $controller = $injector->getInstance(FakeInputValidationController::class);
        $controller->createAction('');
    }
}
