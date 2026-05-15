<?php

namespace Ray\WebFormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\FormValidation;

class FakeController
{
    /**
     * @var FormInterface
     */
    protected $form;

    #[Inject]
    public function setForm(#[Named('contact_form')] FormInterface $form)
    {
        $this->form = $form;
    }

    #[FormValidation]
    public function createAction($name)
    {
        return '201';
    }

    public function createActionValidationFailed()
    {
        return '400';
    }
}
