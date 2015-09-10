<?php

namespace Ray\WebFormModule;

use Ray\Aop\WeavedInterface;
use Ray\Di\Injector;

class AuraInputModuleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $handler = new FakeSessionHandler();
        session_set_save_handler(
            [$handler, 'open'],
            [$handler, 'close'],
            [$handler, 'read'],
            [$handler, 'write'],
            [$handler, 'destroy'],
            [$handler, 'gc']
        );
    }

    public function testAuraInputModule()
    {
        $injector = new Injector(new FakeModule, __DIR__ . '/tmp');
        $controller = $injector->getInstance(FakeController::class);
        $this->assertInstanceOf(WeavedInterface::class, $controller);
    }

    public function testFormModule()
    {
    }
}
