<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\Exception\NoSuchInput;

interface FormInterface
{
    /**
     * Return input element html
     *
     * @param string $input
     *
     * @return string
     *
     * @throws NoSuchInput
     */
    public function input($input);

    /**
     * Return error message
     *
     * @param string $input
     *
     * @return string
     */
    public function error($input);
}
