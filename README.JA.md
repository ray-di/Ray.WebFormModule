# Ray.WebFormModule

[![Continuous Integration](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/continuous-integration.yml)
[![Coding Standards](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/ray-di/Ray.WebFormModule/actions/workflows/coding-standards.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.WebFormModule/?branch=1.x)

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
        $this->install(new WebFormModule());
    }
}
```

> 互換性のため `Ray\WebFormModule\AuraInputModule` クラスも `WebFormModule` の薄い
> サブクラスとして残されています。新規コードでは `WebFormModule` を使ってください。
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

コントローラークラスにフォームをインジェクトします。フォームのバリデーションを行うメソッドを`#[FormValidation]`で
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

    #[Inject]
    public function setForm(#[Named("contact_form")] FormInterface $form)
    {
        $this->contactForm = $form;
    }

    #[FormValidation(form: "contactForm", onFailure: "badRequestAction")]
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

CSRF対策は **opt-in** です。`SetAntiCsrfTrait` を使うフォームには `AntiCsrfInterface` が注入されますが、
トークンの検証は `#[CsrfProtection]` 属性が付いたメソッドでのみ行われます。
`#[CsrfProtection]` が無いメソッドでは、フォーム側に AntiCsrf がセットされていても CSRF チェックは実行されません。

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

セキュリティレベルを高めるためにはユーザーの認証を含んだカスタムCsrfクラスを作成してフォームクラスにセットします。
詳しくはAura.Inputの[Applying CSRF Protections](https://github.com/auraphp/Aura.Input#applying-csrf-protections)をご覧ください。

## 0.x からのマイグレーション

1.0 で Doctrine Annotations を廃止し、PHP 8 Attributes に完全移行しました。
型宣言も強化されています。主な書き換え:

| Before (0.x)                                                       | After (1.0)                                                               |
|--------------------------------------------------------------------|---------------------------------------------------------------------------|
| `@FormValidation(form="f", onFailure="badRequest")`                | `#[FormValidation(form: 'f', onFailure: 'badRequest')]`                   |
| `@FormValidation(form="f", antiCsrf=true)`                         | `#[FormValidation(form: 'f')]` + `#[CsrfProtection]`                      |
| `@InputValidation(form="f")`                                       | `#[InputValidation(form: 'f')]`                                           |
| `@VndError(message="...", logref="...")`                           | `#[VndError(message: '...', logref: '...')]`                              |
| `new AuraInputInterceptor($injector, $reader)`                     | `new AuraInputInterceptor($injector)` (`Reader` 引数は不要)                 |
| `public function input($input)` / `public function error($input)`  | `public function input(string $input): string` / `error(string $input): string` |

破壊的変更の完全なリストは [CHANGELOG.md](CHANGELOG.md) を参照してください。

### Claude Code による自動マイグレーション

リポジトリ同梱の Claude Code skill
[`.claude/skills/migrate-to-1.0/SKILL.md`](.claude/skills/migrate-to-1.0/SKILL.md)
が上記の書き換え (アノテーション → アトリビュート、`antiCsrf=true` の
`#[CsrfProtection]` 分割、`Reader` 引数削除、`FormInterface` 署名更新) を
AI アシスタントに案内します。利用側プロジェクトの `.claude/skills/` に
ディレクトリをコピーして `/migrate-to-1.0` で起動してください。

## Validation Exception

`#[FormValidation]`の代わりに`#[InputValidation]`とアノテートするとバリデーションが失敗したときに`Ray\WebFormModule\Exception\ValidationException`が投げられるよになります。この場合はHTML表現は使われません。Web APIアプリケーションなどに便利です。

```php
use Ray\WebFormModule\Annotation\InputValidation;

class Foo
{
    #[InputValidation(form: "form1")]
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
        $this->install(new WebFormModule());
        $this->override(new FormVndErrorModule());
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

`#[VndError]`属性で`vnd.error+json`に必要な情報を加えることができます。

```php
    #[FormValidation(form: "contactForm")]
    #[VndError(message: "foo validation failed", logref: "a1000", path: "/path/to/error", href: ["_self" => "/path/to/error", "help" => "/path/to/help"])]
```

このオプションのモジュールはAPIアプリケーションの時に有用です。

## Demo

    $ php -S docs/demo/1.csrf/web.php
