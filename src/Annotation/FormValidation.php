<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

/**
 * Validates the input with the form before the method runs and hands a failure to the failure handler
 *
 * @see https://bearsunday.github.io/manuals/1.0/en/form.html#controller
 * @see \Ray\WebFormModule\AuraInputInterceptor
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    /**
     * @param string      $form      Name of the property that holds the form
     * @param string|null $onFailure Method called on failure, `<method>ValidationFailed` when null;
     *                               FormVndErrorModule throws ValidationException without calling it
     */
    public function __construct(
        string $form = 'form',
        public string|null $onFailure = null,
    ) {
        parent::__construct($form);
    }
}
