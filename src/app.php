<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
new Silex\Provider\SessionServiceProvider();

// use from index.php
use Application\Controllers\RegistrationController;
use \Application\Controllers\LoginController;
use \Application\Controllers\GoodsController;
use Application\Controllers\GoodsAdminController;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/../Application/Views']);

$app['registration.controller'] = function () use($app) {
    return new RegistrationController($app);
};

$app['login.controller'] = function () {
    return new LoginController();
};

$app['goods.controller'] = function () {
    return new GoodsController();
};

$app['goodsAdmin.controller'] = function () {
    return new GoodsAdminController();
};

return $app;
