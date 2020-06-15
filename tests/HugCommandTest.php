<?php

namespace Hug\Tests;

use Hug\Command\HugCommand;
use Hug\Service\ParseJson;
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
        $this->path = './ansible/preprod/hosts';
        $this->file = './fichierGoss/goss_projet.yaml';
    }

    public function testExecute(): void
    {
        $app = new Application();
        $parse = new ParseJson();
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
}
