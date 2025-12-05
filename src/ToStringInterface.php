<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

/**
 * Return form markup string
 */
interface ToStringInterface
{
    public function toString(): string;
}
