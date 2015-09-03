<?php
/**
 * This file is part of the Ray.WebFormModule package
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
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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
        if (PHP_SAPI === 'cli') {
            return true;
        }
        if (isset($_POST[self::TOKEN_KEY])) {
            $data[self::TOKEN_KEY] = $_POST[self::TOKEN_KEY];
        }

        return isset($data[self::TOKEN_KEY]) && $data[self::TOKEN_KEY] == $this->getToken();
    }

    private function getToken()
    {
        $value = PHP_SAPI === 'cli' ? self::TEST_TOKEN : $this->session->getCsrfToken()->getValue();

        return $value;
    }
}
