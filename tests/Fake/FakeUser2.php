<?php

namespace Ray\Validation;

use Ray\Validation\Annotation\OnFailure;
use Ray\Validation\Annotation\OnValidate;
use Ray\Validation\Annotation\Valid;

class FakeUser2
{
    public $target;

    /**
     * @Valid
     */
    public function createUser($name)
    {
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

    /**
     * @OnFailure
     */
    public function onFailure(FailureInterface $failure)
    {
        $error = '';
        foreach ($failure->getMessages() as $name => $messages) {
            foreach ($messages as $message) {
                $error .= "Input '{$name}': {$message}" . PHP_EOL;
            }
        }
        $this->target = $failure->getInvocation()->getMethod()->getShortName();

        return $error;
    }
}
