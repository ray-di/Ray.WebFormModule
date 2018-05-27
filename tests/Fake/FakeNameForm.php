<?php

namespace Ray\WebFormModule;

use Aura\Html\Helper\Tag;

class FakeNameForm extends AbstractForm implements ToStringInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setField('name')
            ->setAttribs([
                'id' => 'name',
                'name' => 'name',
                'size' => 20,
                'maxlength' => 20,
                'class' => 'form-control',
                'placeholder' => 'Your Name'
            ]);
        $this->setField('submit', 'submit')
            ->setAttribs([
                'name' => 'submit',
                'value' => 'Submit'
            ]);
        $this->filter->validate('name')->isNot('blank');
        $this->filter->validate('name')->is('alnum');
        $this->filter->useFieldMessage('name', 'Name must be alphabetic only !!.');
    }

    /**
     * {@inheritdoc}
     */
    public function submit()
    {
        return $_POST;
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        $form = $this->form([
            'method' => 'post',
            'action' => '/'
        ]);
        // name
        /** @var $tag Tag */
        $tag  = $this->helper->get('tag');
        $form .= $tag('div', ['class' => 'form-group']);
        $form .= $this->helper->tag('div', ['class' => 'form-group']);
        $form .= $this->helper->tag('label', ['for' => 'name']);
        $form .= 'Name:';
        $form .= $this->helper->tag('/label') . PHP_EOL;
        $form .= $this->input('name');
        $form .= $this->error('name');
        $form .= $this->helper->tag('/div') . PHP_EOL;
        // submit
        $form .= $this->input('submit');
        $form .= $this->helper->tag('/form');

        return $form;
    }
}
