<?php

namespace Ray\WebFormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Annotation\VndError;

class FakeControllerVndError
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
     * @VndError(
     *   message="foo validation failed",
     *   logref="a1000", path="/path/to/error",
     *   href={"_self"="/path/to/error", "help"="/path/to/help"}
     * )
     */
    public function createAction()
    {
    }
}
