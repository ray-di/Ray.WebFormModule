<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;
use Ray\Aop\WeavedInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\WebFormModule\Exception\ValidationException;

class WebFormModuleTest extends TestCase
{
    public function testWebFormModule()
    {
        $injector = new Injector(new class () extends AbstractModule {
            protected function configure()
            {
                $this->install(new WebFormModule());
                $this->bind(FormInterface::class)->annotatedWith('contact_form')->to(FakeForm::class);
            }
        }, __DIR__ . '/tmp');
        $controller = $injector->getInstance(FakeController::class);
        $this->assertInstanceOf(WeavedInterface::class, $controller);
    }

    public function testAuraInputModuleIsAliasOfWebFormModule()
    {
        $this->assertInstanceOf(WebFormModule::class, new AuraInputModule());
    }

    public function testExceptionOnFailure()
    {
        $this->expectException(ValidationException::class);
        $injector = new Injector(new class () extends AbstractModule {
            protected function configure()
            {
                $this->install(new WebFormModule());
                $this->bind(FormInterface::class)->annotatedWith('contact_form')->to(FakeForm::class);
            }
        }, __DIR__ . '/tmp');
        /** @var FakeInputValidationController $controller */
        $controller = $injector->getInstance(FakeInputValidationController::class);
        $controller->createAction('');
    }
}
