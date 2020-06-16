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

    public function setUp(): void
    {
        $this->composerPath = './tests/mock/composer.json';
    }

    public function testExecute(): void
    {
        $init = new ParseJsonService();
        $init->ParseJson($this->composerPath);
    }
}
