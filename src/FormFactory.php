<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;

final class FormFactory
{
    /**
     * @param class-string<AbstractForm> $class
     *
     * @return AbstractForm
     *
     * @psalm-suppress UnsafeInstantiation
     */
    public function newInstance(string $class)
    {
        $form = new $class();
        $form->setBaseDependencies(new Builder(), new FilterFactory(), new HelperLocatorFactory());
        $form->postConstruct();

        return $form;
    }
}
