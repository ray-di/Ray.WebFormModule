<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

/**
 * Base of the attributes that validate the input with a form before the method runs
 */
abstract class AbstractValidation
{
    /** @param string $form Name of the property that holds the form */
    public function __construct(public string $form = 'form')
    {
    }
}
