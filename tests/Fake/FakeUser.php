<?php

namespace Ray\Validation;

use Ray\Validation\Annotation\OnValidate;
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

    /**
     * @return Validation
     *
     * @OnValidate
     */
    public function onValidate($name)
    {
        $result = new Validation;
        if (! is_string($name)) {
            $result->addError('name', 'name should be string');
        }

        return $result;
    }
}
