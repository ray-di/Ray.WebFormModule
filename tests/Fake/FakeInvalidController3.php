<?php

namespace Ray\FormModule;

use Ray\FormModule\Annotation\FormValidation;

class FakeInvalidController3
{
    public $form;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @FormValidation(onFailure="missing_method")
     */
    public function createAction()
    {
    }
}
