<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @var \Silex\Application $app
 */
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $templates = array(
        'error.php'
    );

    return new Response($app['twig']->render('error.php', array('exception' => $e, 'code' => $code)), $code);
});


$app['session']->start();

$app->get('/registration', 'registration.controller:renderRegistrationFormAction');

$app->post('/registration', 'registration.controller:addUserAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/login', 'login.controller:renderLoginFormActon');

$app->post('/login', 'login.controller:userLoginAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

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
    })
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
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
    })
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
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
    })
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/takeToTheBasket', 'goods.controller:takeToTheBasketAction');

$app->get('/showBasket', 'goods.controller:showBasketAction');

$app->get('/deleteProductFromBasket', 'goods.controller:deleteFormBasketAction');

$app->post('/createOrder', 'goods.controller:createOrderAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/logout', 'login.controller:logoutAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/historyOfOrders', 'goods.controller:showHistoryAction');

$app->get('/userProfile', 'userProfile.controller:showUserProfileAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isLogin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->post('/saveUserProfile', 'userProfile.controller:saveUserProfileAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    })
    ->before(function () use ($app) {
        $result = $app['rules']->isLogin();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/restoringPassword', 'login.controller:showFormRestoringPasswordAction');

$app->post('/restoringPassword', 'login.controller:restoringPasswordAction')
    ->before(function () use ($app) {
        $result = $app['rules']->isHaveCsrfToken();
        if (!$result) {
            return new RedirectResponse('/catalogue');
        }
    });

$app->get('/test', function () use ($app) {
    $result = $app['login.controller']->getProductsSortByPriseInLimit('shoes', 0, 2500, 1, 2);
//    throw new Exception('test');
    var_dump($result);
    die();
});