<?php

namespace Ray\WebFormModule;

use Aura\Session\CsrfTokenFactory;
use Aura\Session\Phpfunc;
use Aura\Session\Randval;
use Aura\Session\SegmentFactory;
use Aura\Session\Session;
use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Aop\Arguments;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\WebFormModule\Exception\CsrfViolationException;
use Ray\WebFormModule\Exception\ValidationException;

class AbstractFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractForm
     */
    private $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = (new FormFactory)->newInstance(FakeMiniForm::class);
    }

    /**
     * @param $method
     */
    public function getMethodInvocation(array $arguments)
    {
        // form
        $fakeForm = (new FormFactory)->newInstance(FakeMiniForm::class);
        // controller
        $controller = new FakeController;
        $controller->setForm($fakeForm);
        // interceptor
        $reader = new AnnotationReader;
        $interceptor = new AuraInputInterceptor($reader, new VndErrorHandler($reader));

        return new ReflectiveMethodInvocation(
            $controller,
            new \ReflectionMethod($controller, 'createAction'),
            new Arguments($arguments),
            [
                $interceptor
            ]
        );
    }

    public function testApply()
    {
        $data = ['name' => 'aaa'];
        $isValid = $this->form->apply($data);
        $this->assertTrue($isValid);
    }

    public function testSubmit()
    {
        $this->setExpectedException(ValidationException::class);
        $invocation = $this->getMethodInvocation(['na']);
        $invocation->proceed();
    }

    public function testErrorReturnEmpty()
    {
        $result = $this->form->error('name');
        $expected = '';
        $this->assertSame($expected, $result);
    }

    public function testClone()
    {
        $form = clone $this->form;
        (new \ReflectionProperty($form, 'filter'))->setAccessible(true);
        (new \ReflectionProperty($this->form, 'filter'))->setAccessible(true);
        $this->assertNotSame(spl_object_hash($form), spl_object_hash($this->form));
    }

    public function testGetItelator()
    {
        $itelator = $this->form->getIterator();
        $this->assertInstanceOf(\Iterator::class, $itelator);
    }

    public function testAntiCsrfViolation()
    {
        $this->setExpectedException(CsrfViolationException::class);
        $session = new Session(
            new SegmentFactory,
            new CsrfTokenFactory(new Randval(new Phpfunc)),
            new FakePhpfunc,
            []
        );
        $this->form->setAntiCsrf(new AntiCsrf($session, false));
        $this->form->apply([]);
    }
}
