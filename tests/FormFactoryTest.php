<?php

namespace Ray\WebFormModule;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new FormFactory;
    }

    public function testNewInstance()
    {
        $form = $this->factory->newInstance(FakeMiniForm::class);
        $this->assertInstanceOf(AbstractForm::class, $form);
    }
}
