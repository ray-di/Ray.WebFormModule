<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

class FormValidationError
{
    /** @param array<string, mixed> $value */
    public function __construct(private array $value)
    {
    }

    public function __toString(): string
    {
        return (string) json_encode($this->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
