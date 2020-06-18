<?php

namespace Hug\Tests\Service;

use Hug\Service\ParseJsonService;
use PHPUnit\Framework\TestCase;

class ParseJsonTest extends TestCase
{
    /**
     * @var string
     */
    private $composerPath;
    /**
     * @var string
     */
    private $ansiblePath;
    /**
     * @var string
     */
    private $fakeComposerPath;

    public function setUp(): void
    {
        $this->composerPath = './tests/mock/composer.json';
        $this->ansiblePath = './tests/mock/hosts';
        $this->fakeComposerPath = '.tests/mock/conposer.json';
    }

    public function testExecute(): void
    {
        $init = new ParseJsonService();
        $init->ParseJson($this->ansiblePath, $this->composerPath);
    }
}
