<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Input\Filter;

class ContactForm extends AbstractAuraForm
{
    use SetAntiCsrfTrait;

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

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setField('name', 'text')
             ->setAttribs([
                 'id' => 'name',
                 'size' => 20,
                 'maxlength' => 20,
                 'class' => 'form-control',
                 'placeholder' => 'Your Name'
             ]);
        $this->setField('message', 'textarea')
             ->setAttribs([
                 'id' => 'message',
                 'name' => 'message',
                 'cols' => 40,
                 'rows' => 5,
                 'class' => 'form-control',
                 'placeholder' => 'Message here'
             ]);
        $this->setField('submit', 'submit')
            ->setAttribs([
                 'name' => 'submit',
                 'value' => 'Submit'
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
        $filter->setRule(
            'message',
            'Message must is required.',
            function ($value) {
                return strlen($value) > 0;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function submit()
    {
        return $_POST;
    }
}
