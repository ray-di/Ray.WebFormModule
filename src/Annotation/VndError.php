<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class VndError
{
    /**
     * @param string               $message
     * @param array<string, mixed> $href
     * @param string|null          $logref
     * @param string|null          $path
     *
     * @see http://www.w3.org/TR/html5/links.html#link-type-help
     * @see http://tools.ietf.org/html/rfc6903#section-2
     * @see http://tools.ietf.org/html/rfc6892
     */
    public function __construct(
        public string $message = '',
        public array $href = [],
        public string|null $logref = null,
        public string|null $path = null
    ) {
    }
}
