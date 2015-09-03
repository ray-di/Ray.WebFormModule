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
     * @var array
     */
    private $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return json_encode($this->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
