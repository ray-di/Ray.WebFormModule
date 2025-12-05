<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;
use Aura\Session\Session;

use function is_bool;

use const PHP_SAPI;

final class AntiCsrf implements AntiCsrfInterface
{
    public const TEST_TOKEN = '1234';

    public const TOKEN_KEY = '__csrf_token';

    /** @var bool */
    private $isCli;

    /** @var Session */
    private $session;

    /** @param bool|null $isCli */
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
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function isValid(array $data)
    {
        if ($this->isCli) {
            return true;
        }

        return isset($data[self::TOKEN_KEY]) && $data[self::TOKEN_KEY] === $this->getToken();
    }

    /** @return string */
    private function getToken()
    {
        return $this->isCli ? self::TEST_TOKEN : $this->session->getCsrfToken()->getValue();
    }
}
