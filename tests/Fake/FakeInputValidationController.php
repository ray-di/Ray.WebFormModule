<?php

namespace Ray\WebFormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\InputValidation;

class FakeInputValidationController
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @Inject
     * @Named("contact_form")
     */
    public function setForm(FormInterface $form)
    {
        $this->form  = $form;
    }

    /**
     * @InputValidation
     */
    public function createAction($name)
    {
    }
}
