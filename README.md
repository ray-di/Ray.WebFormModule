# Ray.WebFormModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.WebFormModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.WebFormModule)

An aspect oriented web form module powered by [Aura.Input](https://github.com/auraphp/Aura.Input) and [Ray.Di](https://github.com/ray-di/Ray.Di).

# Getting Started

## Installation

### Composer install

    $ composer require web-form-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\WebFormModule\AuraInputModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new AuraInputModule);
    }
}
```
## Usage

### Form class

We provide two methods on self-initializing form class, one is `init()` method where we add an input field on form and apply fileters and rules. The other method method is `submit()` where it submit data. See more detail at [Aura.Input self-initializing forms](https://github.com/auraphp/Aura.Input/blob/1.x/README.md#self-initializing-forms).

```php
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractForm
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
        $this->filter->validate('name')->is('alnum');
        $this->filter->useFieldMessage('name', 'Name must be alphabetic only.');
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

We annotate the methods which web form validation is required with `@FormValidation`. We can specify form object property name with `name` and failiure method name with `@onFailure`.

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

You can render entire form html when `__toString` is given.

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

## Validation Exception

When we install `Ray\WebFormModule\FormVndErrorModule` as following,

```php
use Ray\Di\AbstractModule;

class FakeVndErrorModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new AuraInputModule);
        $this->override(new FormVndErrorModule);
    }
``` 
A `Ray\WebFormModule\Exception\ValidationException` will be thrown.
We can echo catched exception to get [application/vnd.error+json](https://tools.ietf.org/html/rfc6906) media type. 

```php
echo $e->error;

//{
//    "message": "Validation failed",
//    "path": "/path/to/error",
//    "validation_messages": {
//        "name": [
//            "Name must be alphabetic only."
//        ]
//    }
//}
```

More detail for `vnd.error+json`can be add with `@VndError` annotation. 

```php
    /**
     * @FormValidation(form="contactForm")
     * @VndError(
     *   message="foo validation failed",
     *   logref="a1000", path="/path/to/error",
     *   href={"_self"="/path/to/error", "help"="/path/to/help"}
     * )
     */
```

This optional module is handy for API application. 
   
### Demo

    $ php -S docs/demo/1.csrf/web.php
