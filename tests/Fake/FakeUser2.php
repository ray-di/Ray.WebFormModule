<?php

namespace Ray\Validation;

use Ray\Validation\Annotation\Valid;

class FakeUser2
{
    /**
     * @Valid
     */
    public function createUser($name)
    {
    }

    /**
     * @return Validation
     */
    public function onValidateCreateUser($name)
    {
        $result = new Validation;
        if (! is_string($name)) {
            $result->addError('name', 'name should be string');
        }

        return $result;
    }

    public function onInvalidCreateUser(FailureInterface $failure)
    {
        $error = '';
        foreach ($failure->getMessages() as $name => $messages) {
            foreach ($messages as $message) {
                $error .= "Input '{$name}': {$message}" . PHP_EOL;
            }
        }

        return $error;
    }
}
