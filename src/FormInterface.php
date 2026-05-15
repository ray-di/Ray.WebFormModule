<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

interface FormInterface
{
    /**
     * Return input element html
     *
     * @throws \Aura\Input\Exception\NoSuchInput
     *
     * @return string
     */
    public function input(string $input);

    /** Return error message */
    public function error(string $input) : string;
}
