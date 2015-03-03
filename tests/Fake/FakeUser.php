<?php

namespace Ray\Validation;

use Ray\Validation\Annotation\Valid;

class FakeUser
{
    /**
     * @Valid
     */
    public function createUser($name)
    {
        return true;
    }

    public function onValidateCreateUser($name)
    {
        $result = new ValidationResult;
        if (! is_string($name)) {
            $result->addError('name', 'name should be string');
        }

        return $result;
    }
}
