<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../Application/Views');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');
$app['session.storage.options'] = ['cookie_lifetime' => 3600];
