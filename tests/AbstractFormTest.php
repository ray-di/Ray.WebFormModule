<?php

namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;

class AbstractFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractForm
     */
    private $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = new FakeNameForm;
        $this->form->setBaseDependencies(new Builder, new FilterFactory, new HelperLocatorFactory);
    }

    public function testSubmit()
    {
        $data = [];
        $isValid = $this->form->apply($data);
        $this->assertTrue($isValid);
    }
}
