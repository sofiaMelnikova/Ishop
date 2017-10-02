<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// $app = require __DIR__.'/../src/app.php';

//Request::setTrustedProxies(array('127.0.0.1'));


//$app->get('/', function () use ($app) {
//    return $app['twig']->render('index.html.twig', array());
//})
//->bind('homepage');

/**
 * @var \Silex\Application $app
 */
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});


$app['session']->start();

$app->get('/registration', 'registration.controller:renderRegistrationFormAction');

$app->post('/registration', 'registration.controller:addUserAction');

$app->get('/login', 'login.controller:renderLoginFormActon');

$app->post('/login', 'login.controller:userLoginAction');

$app->get('/catalogue/{kind}/{page}', 'goods.controller:showCatalogAction')
    ->value('kind', 'shoes')
    ->value('page', 1);

$app->get('/addGood', 'goodsAdmin.controller:showFormAddGood')
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/addGood', 'goodsAdmin.controller:addGoodAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/product', 'goods.controller:showProductInfoAction');

$app->get('/adminGoods/{page}', 'goodsAdmin.controller:showAdminGoodsAction')
    ->value('page',1)
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/deleteProduct', 'goodsAdmin.controller:deleteProductAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/editProduct', 'goodsAdmin.controller:changeProductAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/saveChangeProduct', 'goodsAdmin.controller:saveChangeProductAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isLoginAdmin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/takeToTheBasket', 'goods.controller:takeToTheBasketAction');

$app->get('/showBasket', 'goods.controller:showBasketAction');

$app->get('/deleteProductFromBasket', 'goods.controller:deleteFormBasketAction');

$app->post('/createOrder', 'goods.controller:createOrderAction');

$app->get('/logout', 'login.controller:logoutAction');

$app->get('/historyOfOrders', 'goods.controller:showHistoryAction');

$app->get('/test', function () use ($app) {
    (new \Application\Helpers\SavePhoto())->test();

    return $app['twig']->render('addShoes.php');
});