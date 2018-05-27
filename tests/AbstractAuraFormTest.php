<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

class AbstractAuraFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractForm
     */
    private $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = (new FormFactory)->newInstance(FakeForm::class);
    }

    public function testForm()
    {
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data">', $formHtml);
    }

    public function testAntiCsrfForm()
    {
        $this->form->setAntiCsrf(new FakeAntiCsrf);
        $this->form->postConstruct();
        $formHtml = $this->form->form();
        $this->assertSame('<form method="post" enctype="multipart/form-data"><input type="hidden" name="__csrf_token" value="goodvalue" />' . PHP_EOL, $formHtml);
    }

    public function testInput()
    {
        $name = $this->form->input('name');
        $this->assertSame('<input id="name" type="text" name="name" />' . PHP_EOL, (string) $name);
    }

    public function testError()
    {
        $this->form->fill([]);
        $data = ['name' => '@invalid@'];
        $isValid = $this->form->apply($data);
        $this->assertFalse($isValid);
        $error = $this->form->error('name');
        $this->assertSame('Name must be alphabetic only.', $error);
        $html = (string) $this->form;

        return $html;
    }

    /**
     * @depends testError
     */
    public function tesetInputDataReamainedOnValidationFailure($html)
    {
        $expected = '<input id="name" type="text" name="name" value="@invalid@" />';
        $this->assertContains($expected, $html);
    }

    public function testNotToStringImplemented()
    {
        $errNo = $errStr = '';
        set_error_handler(function (int $no, string $str) use (&$errNo, &$errStr) {
            $errNo = $no;
            $errStr = $str;
        });
        $form = new FakeErrorForm;
        (string) $form;
        $this->assertSame(256, $errNo);
        restore_error_handler();
    }
}
