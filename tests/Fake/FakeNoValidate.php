<?php

namespace Ray\Validation;

use Ray\Validation\Annotation\OnFailure;
use Ray\Validation\Annotation\OnValidate;
use Ray\Validation\Annotation\Valid;

class FakeNoValidate
{
    /**
     * @Valid
     */
    public function createUser($name)
    {
    }
}
