<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AbstractValidation
{
    public function __construct(public string $form = 'form')
    {
    }
}
