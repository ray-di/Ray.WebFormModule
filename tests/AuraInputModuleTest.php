<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;
use Ray\Aop\WeavedInterface;
use Ray\Di\Injector;
use Ray\WebFormModule\Exception\ValidationException;

class AuraInputModuleTest extends TestCase
{
    public function testAuraInputModule(): void
    {
        $injector = new Injector(new FakeModule(), __DIR__ . '/tmp');
        $controller = $injector->getInstance(FakeController::class);
        $this->assertInstanceOf(WeavedInterface::class, $controller);
    }

    public function testExceptionOnFailure(): void
    {
        $this->expectException(ValidationException::class);
        $injector = new Injector(new FakeModule(), __DIR__ . '/tmp');
        /** @var FakeInputValidationController $controller */
        $controller = $injector->getInstance(FakeInputValidationController::class);
        $controller->createAction('');
    }
}
