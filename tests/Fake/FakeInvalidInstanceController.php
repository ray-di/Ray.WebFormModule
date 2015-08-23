<?php

namespace Ray\FormModule;

use Ray\FormModule\Annotation\FormValidation;

class FakeInvalidInstanceController
{
    private $form;

    /**
     * @FormValidation(form="form")
     */
    public function createAction()
    {
    }
}
