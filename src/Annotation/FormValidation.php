<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    public bool $antiCsrf = false;

    /** Method name on validation failed */
    public ?string $onFailure;

    public function __construct(
        string $form = 'form',
        bool $antiCsrf = false,
        ?string $onFailure = null
    ) {
        parent::__construct($form);
        $this->antiCsrf = $antiCsrf;
        $this->onFailure = $onFailure;
    }
}
