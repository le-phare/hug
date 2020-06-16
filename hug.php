#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Hug\Command\HugCommand;
use Hug\Service\ParseJsonService;

$app = new Application('hug','1.0.0');
$parseJson = new ParseJsonService();
$command = new HugCommand($parseJson);

$app->add($command);
$app->setDefaultCommand($command->getName(),true);

$app -> run();
