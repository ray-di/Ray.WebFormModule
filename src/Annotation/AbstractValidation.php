<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AbstractValidation
{
    public function __construct(public string $form = 'form')
    {
    }
}
