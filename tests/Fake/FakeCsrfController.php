<?php

namespace Ray\WebFormModule;

use function assert;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\CsrfProtection;
use Ray\WebFormModule\Annotation\FormValidation;

class FakeCsrfController
{
    /**
     * @var FormInterface
     */
    protected $form;

    #[Inject]
    public function setForm(#[Named('mini_form')] FormInterface $form)
    {
        $this->form = $form;
    }

    #[FormValidation]
    #[CsrfProtection]
    public function createAction($name)
    {
        return '201';
    }

    public function formHtml() : string
    {
        assert($this->form instanceof AbstractForm);

        return $this->form->form();
    }
}
