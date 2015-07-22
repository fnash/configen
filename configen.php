<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new \Cilex\Application('ConfigGenerator');

$app->register(new Cilex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->command(new \Configen\Command\ConfigenCommand());

$app->run();
