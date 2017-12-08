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

### Form

`init()`メソッドでフォームの`input`要素を登録とルールの設定を行います。

```php
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\SetAntiCsrfTrait;

class MyForm extends AbstractForm
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
        $this->filter->validate('name')->is('alnum');
        $this->filter->useFieldMessage('name', 'Name must be alphabetic only.');
    }
}
```
メソッドの引数を名前付き引数にしたものがフォームオブジェクトに渡されバリデーションされます。
```php

// このメソッドの場合['id' => $id, 'name' => $name]配列が渡されます
public function createAction($id, $name, $body)
{
```

`Ray\WebFormModule\WebFormModule\SubmitInterface`を実装すると`submit()`メソッドで返された値がフォームオブジェクトに渡されます。
```php
    /**
     * {@inheritdoc}
     */
    public function submit()
    {
        return $_POST;
    }
}
```
`init()`メソッドでで利用できるメソッドについて詳しく`は[Aura.Input](https://github.com/auraphp/Aura.Input#self-initializing-forms)をご覧ください

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

## Validation Exception

`@FormValidation`の代わりに`@InputValidation`とアノテートするとバリデーションが失敗したときに`Ray\WebFormModule\Exception\ValidationException`が投げられるよになります。この場合はHTML表現は使われません。Web APIアプリケーションなどに便利です。

```php
use Ray\WebFormModule\Annotation\InputValidation;

class Foo
{
    /**
     * @InputValidation(form="form1")
     */
    public function createAction($name)
    {
      // ...
    }
```
以下のように `Ray\WebFormModule\FormVndErrorModule`をインストールするとフォームのバリデーションが失敗したときに`Ray\WebFormModule\Exception\ValidationException`例外が投げられるよになります。

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

キャッチした例外の`error`プロパティを`echo`すると[application/vnd.error+json](https://tools.ietf.org/html/rfc6906)メディアタイプの表現が出力されます。 

```php
http_response_code(400);
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

`@VndError`アノテーションで`vnd.error+json`に必要な情報を加えることができます。

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

このオプションのモジュールはAPIアプリケーションの時に有用です。

## Demo

    $ php -S docs/demo/1.csrf/web.php
