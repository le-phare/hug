<?php

namespace Hug\Tests\Command;

use Hug\Command\HugCommand;
use Hug\Service\ParseJsonService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class HugCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $root;
    /**
     * @var Filesystem
     */
    private $fileSystem;
    /**
     * @var string
     */
    private $file;

    public function setUp(): void
    {
        $this->fileSystem = new Filesystem();
        $this->root = './tests/mock/';
        $this->path = $this->root.'hosts';
        $this->file = 'fichierGoss/goss.yaml';
    }

    public function testExecute(): void
    {
        $app = new Application();
        $parse = new ParseJsonService();
        $app->add(new HugCommand($parse));
        $command = $app->find('hug');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--ansible-path' => $this->path, ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode(), 'Return 0 if success');
        $this->assertFileExists($this->file);
        $expectedFile = 'http:
  pp.proxibleus.com:
    status: 200
    body:
      - ext-json
      - ext-test
';
        $this->assertEquals($expectedFile, file_get_contents($this->file));
    }

    public function tearDown(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->file);
    }
}
