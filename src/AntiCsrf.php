<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;
use Aura\Session\Session;

final class AntiCsrf implements AntiCsrfInterface
{
    const TEST_TOKEN = '1234';

    const TOKEN_KEY = '__csrf_token';

    /**
     * @var bool
     */
    private $isCli;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session   $session
     * @param bool|null $isCli
     s     */
    public function __construct(Session $session, $isCli = null)
    {
        $this->session = $session;
        $this->isCli = is_bool($isCli) ? $isCli : PHP_SAPI === 'cli';
    }

    public function setField(Fieldset $fieldset)
    {
        $fieldset->setField(self::TOKEN_KEY, 'hidden')
                 ->setAttribs(['value' => $this->getToken()]);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function isValid(array $data)
    {
        if ($this->isCli) {
            return true;
        }

        return isset($data[self::TOKEN_KEY]) && $data[self::TOKEN_KEY] == $this->getToken();
    }

    /**
     * @return string
     */
    private function getToken()
    {
        $value = $this->isCli ? self::TEST_TOKEN : $this->session->getCsrfToken()->getValue();

        return $value;
    }
}
