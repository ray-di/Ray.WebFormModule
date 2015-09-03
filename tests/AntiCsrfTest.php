<?php

namespace Ray\WebFormModule;

use Aura\Input\Builder;
use Aura\Input\Fieldset;
use Aura\Input\Filter;
use Aura\Session\CsrfTokenFactory;
use Aura\Session\Phpfunc;
use Aura\Session\Randval;
use Aura\Session\SegmentFactory;
use Aura\Session\Session;

class AntiCsrfTest extends \PHPUnit_Framework_TestCase
{
    private $phpfunc;

    /**
     * @var AntiCsrf
     */
    private $antiCsrf;

    /**
     * @var Session
     */
    private $session;

    protected function setUp()
    {
        $this->phpfunc = new FakePhpfunc;
        $this->session = $this->newSession();
        $this->antiCsrf = new AntiCsrf($this->newSession([]));
    }

    protected function newSession(array $cookies = [])
    {
        return new Session(
            new SegmentFactory,
            new CsrfTokenFactory(new Randval(new Phpfunc())),
            $this->phpfunc,
            $cookies
        );
    }

    public function testNew()
    {
        $this->assertInstanceOf(AntiCsrf::class, $this->antiCsrf);
    }

    public function testSetField()
    {
        $result = $this->antiCsrf->setField(new Fieldset(new Builder, new Filter));
        $this->assertNull($result);
    }

    public function testIsValid()
    {
        $data = ['__csrf_token' => AntiCsrf::TEST_TOKEN];
        $this->assertTrue($this->antiCsrf->isValid($data));
    }
}
