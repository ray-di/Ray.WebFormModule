<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

abstract class AbstractValidation
{
    public function __construct(public string $form = 'form')
    {
    }
}
