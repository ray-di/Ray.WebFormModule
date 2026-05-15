<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule\Annotation;

abstract class AbstractValidation
{
    public function __construct(public string $form = 'form')
    {
    }
}
