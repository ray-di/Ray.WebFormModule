<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class VndError
{
    /**
     * @see http://www.w3.org/TR/html5/links.html#link-type-help
     * @see http://tools.ietf.org/html/rfc6903#section-2
     * @see http://tools.ietf.org/html/rfc6892
     */
    public string|null $path;

    /** @param array<string, string> $href */
    public function __construct(
        public string $message = '',
        public array $href = [],
        public string|null $logref = null,
        string|null $path = null,
    ) {
        $this->path = $path;
    }
}
