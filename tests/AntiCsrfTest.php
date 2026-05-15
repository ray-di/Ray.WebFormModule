<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\Builder;
use Aura\Input\Fieldset;
use Aura\Input\Filter;
use Aura\Session\CsrfTokenFactory;
use Aura\Session\Randval;
use Aura\Session\SegmentFactory;
use Aura\Session\Session;
use PHPUnit\Framework\TestCase;

class AntiCsrfTest extends TestCase
{
    /** @var FakePhpfunc */
    private $phpfunc;

    /** @var AntiCsrf */
    private $antiCsrf;

    protected function setUp(): void
    {
        $this->phpfunc = new FakePhpfunc();
        $this->antiCsrf = new AntiCsrf($this->newSession([]), true); // CLI mode for testing
    }

    public function testNew(): void
    {
        $this->assertInstanceOf(AntiCsrf::class, $this->antiCsrf);
    }

    public function testSetField(): void
    {
        $this->antiCsrf->setField(new Fieldset(new Builder(), new Filter()));
        $this->addToAssertionCount(1); // setField returns void, this ensures the test counted as having assertions
    }

    public function testIsValid(): void
    {
        // In CLI mode, isValid always returns true
        $this->assertTrue($this->antiCsrf->isValid([]));
    }

    /** @param array<string, mixed> $cookies */
    protected function newSession(array $cookies = []): Session
    {
        return new Session(
            new SegmentFactory(),
            new CsrfTokenFactory(new Randval()),
            $this->phpfunc,
            $cookies
        );
    }
}
