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

    #[Inject]
    public function setForm(#[Named('contact_form')] FormInterface $form)
    {
        $this->form  = $form;
    }

    #[InputValidation]
    public function createAction($name)
    {
    }
}
