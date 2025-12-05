<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use PHPUnit\Framework\TestCase;

use function restore_error_handler;
use function set_error_handler;

use const PHP_EOL;

class AbstractAuraFormTest extends TestCase
{
    /** @var AbstractForm */
    private $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = (new FormFactory())->newInstance(FakeForm::class);
    }

    public function testForm()
    {
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data">', $formHtml);
    }

    public function testAntiCsrfForm()
    {
        $this->form->setAntiCsrf(new FakeAntiCsrf());
        $this->form->postConstruct();
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data"><input type="hidden" name="__csrf_token" value="goodvalue" />' . PHP_EOL, $formHtml);
    }

    public function testInput()
    {
        $name = $this->form->input('name');
        $this->assertSame('<input id="name" type="text" name="name" />' . PHP_EOL, (string) $name);
    }

    /** @return string */
    public function testError()
    {
        $this->form->fill([]);
        $data = ['name' => '@invalid@'];
        $isValid = $this->form->apply($data);
        $this->assertFalse($isValid);
        $error = $this->form->error('name');
        $this->assertSame('Name must be alphabetic only.', $error);

        return (string) $this->form;
    }

    /**
     * @param string $html
     *
     * @depends testError
     */
    public function tesetInputDataReamainedOnValidationFailure($html)
    {
        $expected = '<input id="name" type="text" name="name" value="@invalid@" />';
        $this->assertStringContainsString($expected, $html);
    }

    public function testNotToStringImplemented()
    {
        $errNo = $errStr = '';
        set_error_handler(static function (int $no, string $str) use (&$errNo, &$errStr) {
            $errNo = $no;
            $errStr = $str;
        });
        $form = new FakeErrorForm();
        (string) $form;
        $this->assertSame(256, $errNo);
        restore_error_handler();
    }
}
