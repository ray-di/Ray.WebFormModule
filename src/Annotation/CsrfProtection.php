<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

/**
 * Makes the validating form check the CSRF token and throw CsrfViolationException on mismatch
 *
 * @see https://bearsunday.github.io/manuals/1.0/en/form.html#csrf-protections
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class CsrfProtection
{
}
