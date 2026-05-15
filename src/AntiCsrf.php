<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

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

    private bool $isCli;

    private Session $session;

    public function __construct(Session $session, bool|null $isCli = null)
    {
        $this->session = $session;
        $this->isCli = is_bool($isCli) ? $isCli : PHP_SAPI === 'cli';
    }

    public function setField(Fieldset $fieldset): void
    {
        $fieldset->setField(self::TOKEN_KEY, 'hidden')
            ->setAttribs(['value' => $this->getToken()]);
    }

    /** @param array $data */
    public function isValid(array $data): bool
    {
        if ($this->isCli) {
            return true;
        }

        return isset($data[self::TOKEN_KEY]) && $data[self::TOKEN_KEY] === $this->getToken();
    }

    private function getToken(): string
    {
        return $this->isCli ? self::TEST_TOKEN : $this->session->getCsrfToken()->getValue();
    }
}
