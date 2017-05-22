<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;

final class FormFactory
{
    /**
     * @param string $class
     *
     * @return AbstractForm
     */
    public function newInstance($class)
    {
        /** @var $form AbstractForm */
        $form = new $class;
        $form->setBaseDependencies(new Builder, new FilterFactory, new HelperLocatorFactory);
        $form->postConstruct();

        return $form;
    }
}
