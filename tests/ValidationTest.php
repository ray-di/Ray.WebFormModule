<?php

namespace Ray\Validation;

namespace Ray\Validation;

use Ray\Di\Injector;
use Ray\Validation\Exception\InvalidArgumentException;
use Ray\Validation\Exception\ValidateMethodNotFound;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        /* @var $user FakeUser */
        $user = (new Injector(new ValidateModule))->getInstance(FakeUser::class);
        $result = $user->createUser('ray');
        $this->assertTrue($result);
    }

    public function testValidateException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        /* @var $user FakeUser2 */
        $user = (new Injector(new ValidateModule))->getInstance(FakeUser::class);
        $user->createUser(null);
    }

    public function testOnInvalid()
    {
        $user = (new Injector(new ValidateModule))->getInstance(FakeUser2::class);
        /* @var $user FakeUser2 */
        $result = $user->createUser(null);
        $this->assertSame("Input 'name': name should be string" . PHP_EOL, $result);
        $this->assertSame('createUser', $user->target);
    }

    public function testNoValidateMethod()
    {
        $this->setExpectedException(ValidateMethodNotFound::class);
        $user = (new Injector(new ValidateModule))->getInstance(FakeNoValidate::class);
        /* @var $user FakeNoValidate */
        $user->createUser(null);
    }
}
