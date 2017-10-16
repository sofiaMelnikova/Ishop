<?php

namespace Application\Controllers;

use Silex\Application;
use Engine\Pagination;
use Symfony\Component\HttpFoundation\Response;

class GoodsController extends BaseControllerAbstract
{
    private $productsOnPage = 3;
    private $showPages = 3;

    /**
     * @param string $kind
     * @param int $page
     * @return Response
     */
    public function showCatalogAction (string $kind, int $page) {
        $this->addCsrfToken();
        $filters = $this->request->query->all();

        $result = (new Pagination())->showCatalog($page, $this->productsOnPage, $this->showPages, $this->app, $this->request,
            $kind, $filters);
        $result = array_merge($result, $filters);

        $getParams = "?";

        foreach ($filters as $key => $value) {
            $getParams = $getParams . $key . "=" . $value . "&";
        }

        $result = array_merge($result, ['filters' => $getParams]);

        return $this->render('catalogue.php', array_merge($result, ['csrfToken' => self::$csrfToken]));
    }

    /**
     * @return Response
     */
    public function showProductInfoAction () {
        $stokeId = intval($this->request->query->get('id'));
        $product = $this->app['good.model']->getAllOfProduct($stokeId);
        return $this->render($product['kinds_value'] . 'Info.php', ['product' => $product]);
    }

    /**
     * @return Response
     */
    public function takeToTheBasketAction () {
        $stokeId = intval($this->request->query->get('id'));
        $userId = $this->app['login.model']->isUserLogin($this->request);
        $product = $this->app['good.model']->getAllOfProduct($stokeId);
        $response = Response::create('', 302, ['Location' => $_SERVER['HTTP_REFERER']]);

        if (!$userId) {
            return $this->app['good.model']->addProductInBasketForLogoutUser($response, $this->request, $product);
        }

        $this->app['good.model']->addProductInBasketForLoginUser($userId, $product);
        return $response;
    }

    /**
     * @return Response
     */
    public function deleteFormBasketAction () {
        $userId = $this->app['login.model']->isUserLogin($this->request);
        $stokeId = intval($this->request->query->get('id'));
        $response = Response::create('', 302, ['Location' => $_SERVER['HTTP_REFERER']]);

        if ($userId) {
            $this->app['good.model']->deleteProductFromBasketForLoginUser($userId, $stokeId);
            $content = $this->app['good.model']->getContentForShowingBasketForLoginUser($userId);
        } else {
            $response = $this->app['good.model']->deleteProductFromBasketForLogoutUser($response, $stokeId, $this->request);
            $content = $this->app['good.model']->getContentForShowingBasketForLogoutUser($this->request);
        }

        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @return Response
     */
    public function showBasketAction () {
        $userId = $this->app['login.model']->isUserLogin($this->request);

        $this->addCsrfToken();
        if ($userId) {
            $result = $this->app['good.model']->getContentForShowingBasketForLoginUser($userId);
            $result = array_merge($result, ['csrfToken' => self::$csrfToken]);
            return $this->render('basket.php', $result);
        }
        $result = $this->app['good.model']->getContentForShowingBasketForLogoutUser($this->request);
        $result = array_merge($result, ['csrfToken' => self::$csrfToken]);
        return $this->render('basket.php', $result);
    }

    /**
     * @return Response
     */
    public function createOrderAction () {

        $userId = $this->app['login.model']->isUserLogin($this->request);
        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);

        if ($userId) {
            $numberOrder = $this->app['good.model']->getNumberOrdesInBasketForUser($userId);
            $this->app['good.model']->executedOrderForLoginUser($numberOrder);
            return $response;
        }

        $userId = $this->app['registration.model']->registrateNewUserByPhone($this->app, $this->request->request->get('phone'));

        if (!empty($userId) && is_array($userId)) {
            $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/showBasket']);
            $response->setContent(json_encode($userId));
            return $response;
        }

        $basket =$this->app['good.model']->getProductsFromBasketForLogoutUser($this->request, false);
        $this->app['good.model']->executedOrderForLogoutUser($userId, $basket);
        return $this->app['good.model']->deleteProductFromBasketForLogoutUser($response);
    }

    /**
     * @return Response
     */
    public function showHistoryAction () {
        $userId = $this->app['login.model']->isUserLogin($this->request);

        if ($userId) {
            $result = $this->app['good.model']->getHistoryForLoginUser($userId);
            return $this->render('historyOfOrders.php', $result);
        }

        $phoneNumber = $this->request->query->get('phone');

        if (empty($phoneNumber)) {
            return $this->render('historyOfOrders.php');
        }

        $result = $this->app['good.model']->getHistoryForLogoutUser($phoneNumber);
        return $this->render('historyOfOrders.php', $result);
    }


}