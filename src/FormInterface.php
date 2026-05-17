<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\Exception\NoSuchInput;

interface FormInterface
{
    /**
     * Return input element html
     *
     * @return string
     *
     * @throws NoSuchInput
     */
    public function input(string $input);

    /** Return error message */
    public function error(string $input): string;
}
