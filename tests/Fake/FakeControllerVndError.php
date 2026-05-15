<?php

namespace Ray\WebFormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\InputValidation;
use Ray\WebFormModule\Annotation\VndError;

class FakeControllerVndError
{
    /**
     * @var FormInterface
     */
    protected $form1;

    #[Inject]
    public function setForm(#[Named('contact_form')] FormInterface $form)
    {
        $this->form1 = $form;
    }

    #[InputValidation(form: 'form1')]
    #[VndError(message: 'foo validation failed', href: ['_self' => '/path/to/error', 'help' => '/path/to/help'], logref: 'a1000', path: '/path/to/error')]
    public function createAction($name)
    {
    }
}
