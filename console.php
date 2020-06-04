#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Hug\Command\hug_command;

$app = new Application();
$command = new hug_command();
$app->add($command);
$app -> run();
