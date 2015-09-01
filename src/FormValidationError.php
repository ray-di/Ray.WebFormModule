<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

class FormValidationError
{
    public $total;

    public $messages = [];

    public function __construct(array $messages)
    {
        $this->total = count($messages);
        $this->messages = $messages;
    }

    public function __toString()
    {
        return json_encode($this->messages);
    }
}
