#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Hug\Command\HugCommand;
use Hug\Service\ParseJson;

$app = new Application();
$parseJson = new ParseJson();
$command = new HugCommand($parseJson);

$app->add($command);

$app -> run();
