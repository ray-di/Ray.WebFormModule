<?php

namespace Ray\Validation;

namespace Ray\Validation;

use Ray\Di\Injector;
use Ray\Validation\Exception\InvalidArgumentException;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $user = (new Injector(new ValidateModule))->getInstance(FakeUser::class);
        $user->createUser(null);
    }

    public function testOnInvalid()
    {
        $user = (new Injector(new ValidateModule))->getInstance(FakeUser2::class);
        $result = $user->createUser(null);
        $this->assertSame("Input 'name': name should be string" . PHP_EOL, $result);
    }
}
