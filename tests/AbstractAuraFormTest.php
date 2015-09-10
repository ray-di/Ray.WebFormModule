<?php

namespace Ray\WebFormModule;

use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;
use Aura\Input\Filter;

class AbstractAuraFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractAuraForm
     */
    private $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = new FakeForm;
        $this->form->setBaseDependencies(new Builder, new Filter);
        $this->form->postConstruct();
        $this->form->setFormHelper(new HelperLocatorFactory);
    }

    public function testForm()
    {
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data">', $formHtml);
    }

    public function testAntiCsrfForm()
    {
        $this->form->setCsrf(new FakeAntiCsrf);
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data"><input type="hidden" name="__csrf_token" value="goodvalue" />' . PHP_EOL, $formHtml);
    }

    public function testInput()
    {
        $name = $this->form->input('name');
        $this->assertSame('<input id="name" type="text" name="name" />' . PHP_EOL, (string) $name);
    }

    public function testError()
    {
        $this->form->fill([]);
        $isValid = $this->form->filter();
        $this->assertFalse($isValid);
        $error = $this->form->error('name');
        $this->assertSame('Name must be alphabetic only.', $error);
    }
}
