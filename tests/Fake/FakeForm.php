<?php

namespace Ray\WebFormModule;

use Aura\Input\Filter;

class FakeForm extends AbstractAuraForm
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
        /** @var $filter Filter */
        $filter = $this->getFilter();
        $filter->setRule(
            'name',
            'Name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );
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
        $form .= $this->helper->tag('div', ['class' => 'form-group']);
        $form .= $this->helper->tag('label', ['for' => 'name']);
        $form .= 'Name:';
        $form .= $this->helper->tag('/label') . PHP_EOL;
        $form .= $this->input('name');
        $form .= $this->error('name');
        $form .= $this->helper->tag('/div') . PHP_EOL;
        // message
        $form .= $this->helper->tag('div', ['class' => 'form-group']);
        $form .= $this->helper->tag('label', ['for' => 'message']);
        $form .= 'Message:';
        $form .= $this->helper->tag('/label') . PHP_EOL;
        $form .= $this->input('message');
        $form .= $this->error('message');
        $form .= $this->helper->tag('/div') . PHP_EOL;
        // submit
        $form .= $this->input('submit');
        $form .= $this->helper->tag('/form');

        return $form;
    }
}
