<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

interface FailureInterface
{
    /**
     * Return validation error message
     *
     * @return string[]
     */
    public function getMessages();
}
