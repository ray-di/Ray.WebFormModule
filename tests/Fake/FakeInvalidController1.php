<?php

namespace Ray\WebFormModule;

use Ray\WebFormModule\Annotation\FormValidation;

class FakeInvalidController1
{
    /**
     * @FormValidation(form="missing")
     */
    public function createAction()
    {
    }
}
