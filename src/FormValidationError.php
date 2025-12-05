<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

class FormValidationError
{
    /** @var array<string, mixed> */
    private $value;

    /** @param array<string, mixed> $value */
    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return json_encode($this->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
