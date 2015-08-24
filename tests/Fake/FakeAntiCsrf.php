<?php

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;

final class FakeAntiCsrf implements AntiCsrfInterface
{
    protected $value = 'goodvalue';

    protected $name = '__csrf_token';

    public function setField(Fieldset $fieldset)
    {
        $fieldset->setField($this->name, 'hidden')
                 ->setValue($this->value);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function isValid(array $data)
    {
        return true;
    }
}
