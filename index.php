<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new \Cilex\Application('ConfigGenerator');

$app->command(new \Configen\Command\ConfigenCommand());

$app->run();
