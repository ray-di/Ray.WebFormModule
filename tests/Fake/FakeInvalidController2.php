<?php

namespace Ray\FormModule;

use Ray\FormModule\Annotation\FormValidation;

class FakeInvalidController2
{
    private $form = null;

    /**
     * @FormValidation(form="form")
     */
    public function createAction()
    {
    }
}
