<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

class FormValidationError
{
    /**
     * @var int
     */
    public $total;

    /**
     * @var array
     */
    public $messages = [];

    /**
     * @var
     */
    public $path;


    public function __construct($path, array $messages)
    {
        $this->path = $path;
        $this->total = count($messages);
        $this->messages = $messages;
    }

    public function __toString()
    {
        $vndError = [
            'path' => $this->path,
            'message' => 'Validation failed',
            'validation_messages' => [$this->messages]
        ];

        return json_encode($vndError, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
