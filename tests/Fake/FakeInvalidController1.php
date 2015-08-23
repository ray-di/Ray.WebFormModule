<?php

namespace Ray\FormModule;

use Ray\FormModule\Annotation\FormValidation;

class FakeInvalidController1
{
    /**
     * @FormValidation(form="missing")
     */
    public function createAction()
    {
    }
}
