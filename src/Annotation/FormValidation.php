<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    public function __construct(
        string $form = 'form',
        public string|null $onFailure = null,
    ) {
        parent::__construct($form);
    }
}
