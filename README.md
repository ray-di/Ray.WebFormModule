# Ray.WebFormModule

[![Continuous Integration](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/continuous-integration.yml)
[![Coding Standards](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/coding-standards.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)

An aspect oriented web form module powered by [Aura.Input](https://github.com/auraphp/Aura.Input) and [Ray.Di](https://github.com/ray-di/Ray.Di).

# Getting Started

## Installation

### Composer install

    $ composer require ray/web-form-module
 
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

We annotate the methods which web form validation is required with `#[FormValidation]`. We can specify form object property name with `form` and failure method name with `onFailure`.

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

    #[Inject]
    public function setForm(#[Named("contact_form")] FormInterface $form)
    {
        $this->contactForm = $form;
    }

    #[FormValidation(form: "contactForm", onFailure: "badRequestAction")]
    public function createAction()
    {
        // validation success
        // More detail for `vnd.error+json` can be added with `#[VndError]`.
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

CSRF protection is **opt-in**. A form that uses `SetAntiCsrfTrait` is wired
with an `AntiCsrfInterface`, but the token is only verified when the
validated method is annotated with `#[CsrfProtection]`. Methods without
`#[CsrfProtection]` perform no CSRF check even if the form supports it.

```php
use Ray\WebFormModule\AbstractAuraForm;
use Ray\WebFormModule\Annotation\CsrfProtection;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractAuraForm
{
    use SetAntiCsrfTrait;
}

class MyController
{
    #[FormValidation(form: "contactForm")]
    #[CsrfProtection]
    public function createAction()
    {
    }
}
```
You can provide your custom `AntiCsrf` class. See more detail at [Aura.Input](https://github.com/auraphp/Aura.Input#applying-csrf-protections)

## Migration from 0.x

Version 1.0 drops Doctrine Annotations in favour of native PHP 8 Attributes
and tightens type declarations. The most common rewrites:

| Before (0.x)                                                       | After (1.0)                                                               |
|--------------------------------------------------------------------|---------------------------------------------------------------------------|
| `@FormValidation(form="f", onFailure="badRequest")`                | `#[FormValidation(form: 'f', onFailure: 'badRequest')]`                   |
| `@FormValidation(form="f", antiCsrf=true)`                         | `#[FormValidation(form: 'f')]` + `#[CsrfProtection]`                      |
| `@InputValidation(form="f")`                                       | `#[InputValidation(form: 'f')]`                                           |
| `@VndError(message="...", logref="...")`                           | `#[VndError(message: '...', logref: '...')]`                              |
| `new AuraInputInterceptor($injector, $reader)`                     | `new AuraInputInterceptor($injector)` (no `Reader` argument)              |
| `public function input($input)` / `public function error($input)`  | `public function input(string $input): string` / `error(string $input): string` |

See [CHANGELOG.md](CHANGELOG.md) for the full list of breaking changes.

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

More detail for `vnd.error+json` can be added with the `#[VndError]` attribute.

```php
    #[FormValidation(form: "contactForm")]
    #[VndError(message: "foo validation failed", logref: "a1000", path: "/path/to/error", href: ["_self" => "/path/to/error", "help" => "/path/to/help"])]
```

This optional module is handy for API application. 
   
### Demo

    $ php -S docs/demo/1.csrf/web.php
