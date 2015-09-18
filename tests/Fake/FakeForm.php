<?php

namespace Ray\WebFormModule;

class FakeForm extends AbstractForm
{
    use SetAntiCsrfTrait;

    /**
     * @var array
     */
    private $submit = [];

    public function setSubmit(array $submit)
    {
        $this->submit = $submit;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setField('name', 'text')
             ->setAttribs([
                 'id' => 'name'
             ]);
        $this->filter->validate('name')->is('alnum');
        $this->filter->useFieldMessage('name', 'Name must be alphabetic only.');
    }

    /**
     * {@inheritdoc}
     */
    public function submit()
    {
        return $this->submit;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $form = $this->form();
        // name
        $form .= $this->input('name');
        $form .= $this->error('name');

        return $form;
    }
}
