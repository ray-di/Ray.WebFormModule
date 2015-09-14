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
    public function createAction($name)
    {
        return '201';
    }

    public function badRequestAction()
    {
        return '400';
    }
}
