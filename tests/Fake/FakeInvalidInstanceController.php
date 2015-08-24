<?php

namespace Ray\WebFormModule;

use Ray\WebFormModule\Annotation\FormValidation;

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
