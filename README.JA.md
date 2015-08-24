# Ray.WebFormModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.WebFormModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.WebFormModule)

Ray.WebFormModuleはアスペクト指向でフォームのバリデーションを行うモジュールです。
フォームライブラリには[Aura.Input](https://github.com/auraphp/Aura.Input)を使い、
特定のアプリケーションフレームワークの依存なしで利用できます。

## Installation

### Composer install

    $ composer require ray/web-form-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\WebFormModule\WebFormModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new WebFormModule);
    }
}
```
## Usage

### Form

フォームの`input`要素を登録する`init()`メソッドとフォーム送信を行う`submit()`メソッドを持つフォームクラスを用意します。

```php
use Ray\WebFormModule\AbstractAuraForm;
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractAuraForm
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // set input fields
        $this->setField('name', 'text')
             ->setAttribs([
                 'id' => 'name'
             ]);
        // set input filters
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
}
```
 * `init()`メソッドではinput属性を指定してフォームを登録し、フィルターやルールを適用します。
フォームクラスで利用できるメソッドについて詳しく`は[Aura.Input](https://github.com/auraphp/Aura.Input#self-initializing-forms)をご覧ください

 * `submit()メソッド`ではフォームでバリデーションを行うための`$_POST`や`$_GET`を返します。

### Controller

コントローラークラスにフォームをインジェクトします。フォームのバリデーションを行うメソッドを`@FormValidation`で
アノテートします。この時フォームのプロパティ名を`form`で、バリデーションが失敗したときのメソッドを`onFailure`で指定します。

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

フォームの`input`要素やエラーメッセージを取得するには要素名を指定します。

```php
  echo $form->input('name'); // <input id="name" type="text" name="name" size="20" maxlength="20" />
  echo $form->error('name'); // "Name must be alphabetic only." or blank.
```

### CSRF Protections

CSRF対策を行うためにはフォームにCSRFオブジェクトをセットします。

```php
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractAuraForm
{
    use SetAntiCsrfTrait;
```

セキュリティレベルを高めるためにはユーザーの認証を含んだカスタムCsrfクラスを作成してフォームクラスにセットします。
詳しくはAura.Inputの[Applying CSRF Protections](https://github.com/auraphp/Aura.Input#applying-csrf-protections)をご覧ください。

## Demo

    $ php -S docs/demo/1.csrf/web.php

## Requirements

 * PHP 5.5+
 * hhvm

