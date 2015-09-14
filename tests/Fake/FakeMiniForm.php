<?php

namespace Ray\WebFormModule;

use Aura\Input\Filter;

class FakeMiniForm extends AbstractForm
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setField('name', 'text')
             ->setAttribs([
                 'id' => 'name'
             ]);
        $this->filter->validate('name')->is('strlenMin', 3);
    }
}
