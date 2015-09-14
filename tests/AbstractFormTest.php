<?php

namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;
use Aura\Input\Filter;
use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Aop\Arguments;
use Ray\Aop\ReflectiveMethodInvocation;
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
        $this->form = new FakeMiniForm;
        $this->form->setBaseDependencies(new Builder, new FilterFactory, new HelperLocatorFactory);
        $this->form->postConstruct();
    }

    /**
     * @param $method
     */
    public function getMethodInvocation(array $arguments)
    {
        // form
        $fakeForm = new FakeMiniForm;
        $fakeForm->setBaseDependencies(new Builder, new FilterFactory, new HelperLocatorFactory);
        $fakeForm->postConstruct();
        // controller
        $controller = new FakeController;
        $controller->setForm($fakeForm);
        // interceptor
        $reader = new AnnotationReader;
        $interceptor = new AuraInputInterceptor($reader, new VndErrorHandler($reader));
        return new ReflectiveMethodInvocation(
            $controller,
            new \ReflectionMethod($controller, 'createAction'),
            new Arguments([$arguments]),
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
}
