<?php
/**
 * This file is part of the Ray.FormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\FormModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class FormValidation
{
    /**
     * @var bool
     */
    public $antiCsrf = false;

    /**
     * Method name on validation faild.
     *
     * @var string
     */
    public $onFailure;

    /**
     * @var string
     */
    public $form = 'form';
}
