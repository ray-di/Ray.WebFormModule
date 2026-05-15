<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

interface SubmitInterface
{
    /**
     * Return subject value
     *
     * @return array<string, mixed>|object
     */
    public function submit();
}
