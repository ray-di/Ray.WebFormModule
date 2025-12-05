<?php

declare(strict_types=1);

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
        /** @var AbstractForm $form */
        $form = new $class();
        $form->setBaseDependencies(new Builder(), new FilterFactory(), new HelperLocatorFactory());
        $form->postConstruct();

        return $form;
    }
}
