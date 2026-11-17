<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

/**
 * Validates the input with the form before the method runs and throws ValidationException on failure
 *
 * @see https://bearsunday.github.io/manuals/1.0/en/form.html#validation-exception
 * @see \Ray\WebFormModule\InputValidationInterceptor
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class InputValidation extends AbstractValidation
{
}
