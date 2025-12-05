<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;

class FormFactoryTest extends TestCase
{
    /**
     * @var FormFactory
     */
    private $factory;

    protected function setUp(): void
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
