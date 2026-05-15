<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;

class FormFactoryTest extends TestCase
{
    /** @var FormFactory */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new FormFactory();
    }

    public function testNewInstance(): void
    {
        $form = $this->factory->newInstance(FakeMiniForm::class);
        $this->assertInstanceOf(AbstractForm::class, $form);
    }
}
