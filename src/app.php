<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
new Silex\Provider\SessionServiceProvider();

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$token = (new \Application\Helpers\RandomString())->get();

$app['registration.controller'] = function () use ($app, $request) {

    return new \Application\Controllers\RegistrationController($app, $request);
};

$app['login.controller'] = function () use ($app, $request) {
    return new \Application\Controllers\LoginController($app, $request);
};

$app['goods.controller'] = function () use ($app, $request) {
    return new \Application\Controllers\GoodsController($app, $request);
};

$app['goodsAdmin.controller'] = function () use ($app, $request) {
    return new \Application\Controllers\GoodsAdminController($app, $request);
};

$app['userProfile.controller'] = function () use ($app, $request) {
    return new \Application\Controllers\UserProfileController($app, $request);
};

$app['good.model'] = function () use ($app) {
    return new \Application\Models\GoodModel($app);
};

$app['login.model'] = function () use ($app) {
    return new \Application\Models\LoginModel($app);
};

$app['registration.model'] = function () use ($app) {
    return new \Application\Models\RegistrationModel($app);
};

$app['userProfile.model'] = function () use ($app) {
    return new \Application\Models\UserProfileModel($app);
};

$app['RandomString.helper'] = function () {
  return new \Application\Helpers\RandomString();
};

$app['uploader.helper'] = function () {
    return new \Application\Helpers\Uploader();
};

$app['rules'] = function () use ($app, $request) {
    return new \Application\Models\Rules($app, $request);
};

$app['shoes.fields'] = function () use ($request) {
    return new \Application\ValueObject\ShoesFields($request);
};

$app['jacket.fields'] = function () use ($request) {
    return new \Application\ValueObject\JacketFields($request);
};

$app['plaid.fields'] = function () use ($request) {
  return new \Application\ValueObject\PlaidFields($request);
};

$app['DbQuery'] = function () use ($app) {
  return new \Engine\DbQuery('Ishop', '127.0.0.1', 'root', 'qwerty133', $app);
};

return $app;
