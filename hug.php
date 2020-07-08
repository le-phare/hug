#!/usr/bin/env php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Hug\Command\HugCommand;
use Hug\Service\ParseJsonService;
use Symfony\Component\Console\Application;

$app = new Application('hug', '@release-date@');
$parseJson = new ParseJsonService();
$command = new HugCommand($parseJson);

$app->add($command);
$app->setDefaultCommand($command->getName(), true);

$app->run();
