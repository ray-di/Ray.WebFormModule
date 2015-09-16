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

    /**
     * @Inject
     * @Named("contact_form")
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @FormValidation
     *
     * = is same as @ FormValidation(form="form", onFailure="createActionValidationFailed")
     */
    public function createAction($name)
    {
        return '201';
    }

    public function createActionValidationFailed()
    {
        return '400';
    }
}
