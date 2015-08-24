# Ray.WebFormModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.WebFormModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.WebFormModule)

Web form ([Aura.Input](https://github.com/auraphp/Aura.Input)) module for `Ray.Di`

## Installation

### Composer install

    $ composer require ray/form-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\WebFormModule\FormModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new FormModule);
    }
}
```
## Usage

### Form

```php
use Ray\WebFormModule\AbstractAuraForm;
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractAuraForm
{
    // for anti CSRF
    use SetAntiCsrfTrait;

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
        return $_POST;
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
        // submit
        $form .= $this->input('submit');
        $form .= $this->helper->tag('/form');

        return $form;
    }
}
```
### Controller

```php
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class MyController
{
    /**
     * @var FormInterface
     */
    protected $contactForm;

    /**
     * @Inject
     * @Named("contact_form")
     */
    public function setForm(FormInterface $form)
    {
        $this->contactForm = $form;
    }

    /**
     * @FormValidation(form="contactForm", onFailure="badRequestAction")
     */
    public function createAction()
    {
        // validation success
    }

    public function badRequestAction()
    {
        // validation faild
    }
}
```
### View

You can render entire form html when `__toString` is provided.
```php
  echo $form; // render entire form html
```

or render input element basis.

```php
  echo $form->input('name'); // <input id="name" type="text" name="name" size="20" maxlength="20" />
  echo $form->error('name'); // "Name must be alphabetic only." or blank.
```
## CSRF Protections

```php
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyController 
{
    use SetAntiCsrfTrait;
```
You can provide your custom `AntiCsrf` class. See more detail at [Aura.Input](https://github.com/auraphp/Aura.Input#applying-csrf-protections)

### Demo

    $ php -S docs/demo/1.csrf/web.php

### Requirements

 * PHP 5.5+
 * hhvm

