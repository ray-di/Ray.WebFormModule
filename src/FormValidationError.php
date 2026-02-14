<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

class FormValidationError
{
    private array $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return json_encode($this->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
