<?php
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
     * @param string $input
     *
     * @throws \Aura\Input\Exception\NoSuchInput
     *
     * @return string
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
