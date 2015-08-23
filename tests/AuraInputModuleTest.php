<?php

namespace Ray\FormModule;

use Ray\Aop\WeavedInterface;
use Ray\Di\Injector;

class AuraInputModuleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $handler = new FakeSessionHandler();
        session_set_save_handler(
            array($handler, 'open'),
            array($handler, 'close'),
            array($handler, 'read'),
            array($handler, 'write'),
            array($handler, 'destroy'),
            array($handler, 'gc')
        );
    }

    public function testAuraInputModule()
    {
        $injector = new Injector(new FakeModule);
        $controller = $injector->getInstance(FakeController::class);
        $this->assertInstanceOf(WeavedInterface::class, $controller);
    }

    public function testFormModule()
    {

    }

}
