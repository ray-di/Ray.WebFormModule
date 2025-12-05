c<?php
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
#[\Attribute(\Attribute::TARGET_METHOD)]
class AbstractValidation
{
    /**
     * @var string
     */
    public $form = 'form';

    public function __construct(string $form = 'form')
    {
        $this->form = $form;
    }
}
