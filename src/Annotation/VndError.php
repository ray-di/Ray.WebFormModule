<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

/**
 * Sets the message, path, logref and links of the vnd.error body when validation fails
 * VndErrorHandler reads it for #[InputValidation], and for #[FormValidation] when FormVndErrorModule is installed
 *
 * @see https://bearsunday.github.io/manuals/1.0/en/form.html#validation-exception
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class VndError
{
    /**
     * @param array<string, mixed> $href URIs keyed by link relation, e.g. `help`
     *
     * @see http://www.w3.org/TR/html5/links.html#link-type-help
     * @see http://tools.ietf.org/html/rfc6903#section-2
     * @see http://tools.ietf.org/html/rfc6892
     */
    public function __construct(
        public string $message = '',
        public array $href = [],
        public string|null $logref = null,
        public string|null $path = null,
    ) {
    }
}
