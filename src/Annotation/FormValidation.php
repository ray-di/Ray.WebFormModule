<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    /** Method name on validation failed */
    public string|null $onFailure;

    public function __construct(
        string $form = 'form',
        public bool $antiCsrf = false,
        string|null $onFailure = null,
    ) {
        parent::__construct($form);

        $this->onFailure = $onFailure;
    }
}
