<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class VndError
{
    /**
     * @var string
     *
     * REQUIRED
     */
    public $message;

    /**
     * @var array
     *
     * REQUIRED
     */
    public $href;

    /**
     * @var string
     *
     * OPTIONAL
     */
    public $logref;

    /**
     * @var string
     *
     * OPTIONAL
     *
     * help
     * @see http://www.w3.org/TR/html5/links.html#link-type-help
     *
     * about
     * @see http://tools.ietf.org/html/rfc6903#section-2
     *
     * describes
     * @see http://tools.ietf.org/html/rfc6892
     */
    public $path;
}
