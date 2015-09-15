<?php

namespace Ray\WebFormModule;

use Ray\WebFormModule\Annotation\FormValidation;

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
    public function createAction($name)
    {
    }
}
