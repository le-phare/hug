#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Hug\Command\hug_command;
use Hug\Parse\parseJson;

$app = new Application();
$parseJson = new parseJson();
$command = new hug_command();

$app->add($command);

$app -> run();
