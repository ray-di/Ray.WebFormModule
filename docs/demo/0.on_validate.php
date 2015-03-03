<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Ray\Di\Injector;
use Ray\Validation\Annotation\Valid;
use Ray\Validation\Exception\InvalidArgumentException;
use Ray\Validation\ValidateModule;
use Ray\Validation\Validation;

class Fake
{
    /**
     * @Valid
     */
    public function foo($name)
    {
    }

    public function onValidateFoo($name)
    {
        $validation = new Validation;
        if (! is_string($name)) {
            $validation->addError('name', 'name should be string.');
        }

        return $validation;
    }
}

$fake = (new Injector(new ValidateModule))->getInstance(Fake::class);
try {
    $fake->foo(0);
} catch (Exception $e) {
    $works = $e instanceof InvalidArgumentException;
}
echo($works ? 'It works!' : 'It DOES NOT work!') . PHP_EOL;
