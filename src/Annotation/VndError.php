<?php
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
    public string $message;

    /** @var array<string, string> */
    public array $href;

    public ?string $logref;

    /**
     * @see http://www.w3.org/TR/html5/links.html#link-type-help
     * @see http://tools.ietf.org/html/rfc6903#section-2
     * @see http://tools.ietf.org/html/rfc6892
     */
    public ?string $path;

    /** @param array<string, string> $href */
    public function __construct(
        string $message = '',
        array $href = [],
        ?string $logref = null,
        ?string $path = null
    ) {
        $this->message = $message;
        $this->href = $href;
        $this->logref = $logref;
        $this->path = $path;
    }
}
