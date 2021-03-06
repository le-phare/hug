<?php

namespace Hug\Tests\Command;

use Hug\Service\ParseJsonService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class HugCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $comPath;
    /**
     * @var string
     */
    private $ansPath;
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
    /**
     * @var string
     */
    private $sonde;

    public function setUp(): void
    {
        $this->fileSystem = new Filesystem();
        $this->root = './tests/mock/';
        $this->ansPath = $this->root.'hosts';
        $this->comPath = $this->root.'composer.json';
        $this->file = 'generatedFiles/goss.yaml';
        $this->sonde = 'generatedFiles/sonde_faros.php';
    }

    public function testExecute(): void
    {
        $parse = new ParseJsonService();
        $parse->ParseJson($this->ansPath, $this->comPath);
        $this->assertFileExists($this->file);
        $this->assertFileExists($this->sonde);
        $expectedGossFile = "package:
  git:
    installed: true
  curl:
    installed: true
  apache2:
    installed: true
port:
  'tcp:443':
    listening: true
  'tcp:80':
    listening: true
  'tcp:22':
    listening: true
  'tcp:9418':
    listening: true
file:
  ~/.ssh/authorized_keys:
    exists: true
    mode: '0600'
command:
  diff:
    exit-status: 0
    exec: 'diff ~/.ssh/authorized_keys ~/.test/lephare.keys -s -q'
  ssh:
    exit-status: 0
    exec: 'ssh -oStrictHostKeyChecking=no git@gitlab.com -T && ssh -oStrictHostKeyChecking=no git@gitlab.lephare.io -T && ssh -oStrictHostKeyChecking=no bitbucket.org -T && ssh -oStrictHostKeyChecking=no git@github.com -T'
http:
  pp.proxibleus.com/sonde_faros.php:
    status: 200
    body:
      - 'DocumentRoot:OK'
      - ctype
      - iconv
      - json
      - pcre
      - session
      - SimpleXML
      - tokenizer
      - curl
      - gd
      - intl
      - mbstring
      - pdo
      - pdo-pgsql
      - pgsql
      - posix
      - xml
      - opcache
      - memcached
      - imagick
      - apcu
      - apcu-bc
      - exif
      - zip
      - soap
      - short_open_tag=off
      - magic_quotes_gpc=off
      - register_globals=off
      - session.autostart=off
      - date.timezone=Europe/Paris
      - upload_max_filesize=32M
      - post_max_size=33M
      - sys_temp_dir=/var/tmp
      - upload_dir=/var/tmp
      - session.save_handler=memcached
      - 'session.save_path=localhost:11211'
      - memcached.sess_lock_wait_min=150
      - memcached.sess_lock_wait_max=150
      - memcached.sess_lock_retries=800
      - opcache.revalidate_freq=0
      - opcache.validate_timestamps=0
      - opcache.max_accelerated_files=7963
      - opcache.memory_consumption=192
      - opcache.interned_strings_buffer=16
      - opcache.fast_shutdown=1
      - test
      - test2
";
        $this->assertEquals($expectedGossFile, file_get_contents($this->file));
    }

    public function tearDown(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->file);
        $fileSystem->remove($this->sonde);
    }
}
