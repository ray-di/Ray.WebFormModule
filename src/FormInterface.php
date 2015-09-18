<?php
/**
 * This file is part of the Ray.WebFormModule package
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
     * @return string
     *
     * @throws \Aura\Input\Exception\NoSuchInput
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
