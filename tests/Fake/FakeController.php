<?php

namespace Ray\FormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\FormModule\Annotation\FormValidation;

class FakeController
{
    /**
     * @var FormInterface
     */
    protected $form1;

    /**
     * @Inject
     * @Named("contact_form")
     */
    public function setForm(FormInterface $form)
    {
        $this->form1 = $form;
    }

    /**
     * @FormValidation(form="form1", onFailure="badRequestAction")
     */
    public function createAction()
    {
        return '201';
    }

    public function badRequestAction()
    {
        return '400';
    }
}
