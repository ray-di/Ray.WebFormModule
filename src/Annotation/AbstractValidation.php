<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class AbstractValidation
{
    /**
     * @var string
     */
    public $form = 'form';
}
