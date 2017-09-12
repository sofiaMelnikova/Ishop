<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Engine\Pagination;
use Application\Models\LoginModel;
use Symfony\Component\HttpFoundation\Response;

class GoodsController extends BaseController
{
    private $productsOnPage = 4;
    private $showPages = 3;

    /**
     * @param string $kind
     * @param int $actualPage
     * @return array
     */
    public function showCatalogAction (string $kind, int $actualPage, Request $request) {
        $loginModel = $this->newLoginModel();
        $goodModel = $this->newGoodModel();
        $countProducts = $goodModel->getCountProducts($kind);
        $pagination = new Pagination();
        $countPages = $pagination->getCountPagesOrGroups($countProducts, $this->productsOnPage);
        if ($actualPage > $countPages) {
            $actualPage = $countPages;
        }
        $pagesMinMax = $pagination->getMainMaxPages($actualPage, $this->showPages, $countPages);
        $productsMinMax = $pagination->getMinMaxElementsOnPage($actualPage, $this->productsOnPage);
        $products = $goodModel->getNamePicturePriceOfKind($kind, $productsMinMax['min'], $this->productsOnPage);
        $countProducts = $goodModel->countProducts($request);
        $id = $loginModel->isUserLogin($request);
        $admin = false;
        $login = false;
        if ($id) {
            $admin = $loginModel->isAdmin($id);
            $login = $loginModel->getLogin($id);
        }
        return ['products' => $products, 'pages' => $pagesMinMax, 'kind' => $kind, 'sumPages' => $countPages,
            'countProducts' => $countProducts, 'admin' => $admin, 'login' => $login];

    }

    /**
     * @param Request $request
     * @return array
     */
    public function showProductInfoAction (Request $request) {
        $stokeId = intval($request->get('id'));
        $goodModel = $this->newGoodModel();
        $product = $goodModel->getAllOfProduct($stokeId);
        return ['product' => $product];
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return bool|Response
     */
    public function takeToTheBasketAction (Request $request, Response $response) {
        $goodModel = $this->newGoodModel();
        $loginModel = new LoginModel();
        $userId = $loginModel->isUserLogin($request);
        if (!$userId) {
            return false; // Error: user is not login return loginPage
        }
        $stokeId = intval($request->get('id'));

        return $goodModel->addProductInBasket($stokeId, $response, $request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool|Response
     */
    public function NEWTakeToTheBasketAction (Request $request, Response $response) {
        $goodModel = $this->newGoodModel();
        $loginModel = new LoginModel();
        $userId = $loginModel->isUserLogin($request);
        if (!$userId) {
            $stokeId = intval($request->get('id'));
            return $goodModel->addProductInBasket($stokeId, $response, $request);
            // return false; // Error: user is not login return loginPage
        }

    }

    /**
     * @param Response $response
     * @param Request $request
     * @return Response
     */
    public function deleteFormBasketAction (Response $response, Request $request) {
        $goodModel = $this->newGoodModel();
        $stokeId = intval($request->get('id'));
        $goodModel->deleteProductFromBasket($stokeId, $response, $request);
        $content = $goodModel->getContentFromBasket($request);
        $response->setContent($content);
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function showBasketAction (Request $request) {
        $resultSum = 0;
        $goodModel= $this->newGoodModel();
        $basketProducts = $goodModel->getProductsFromBasket($request);
        $products = [];
        foreach ($basketProducts as $key => $value) {
            $product = $goodModel->getAllOfProduct($key);
            $sum = $product['cost'] * $value;
            $resultSum = $resultSum + $sum;
            $product = array_merge($product, ['countInBasket' => $value, 'sum' => $sum]);
            array_push($products, $product);
        }
        return ['products' => $products,'resultSum' => $resultSum];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createOrderAction (Request $request, Response $response) {
        $loginModel = new LoginModel();
        $userId = $loginModel->isUserLogin($request);
        if ($userId) { // if user login

        }
        // user enter phone number
        $goodModel= $this->newGoodModel();
        $basket = $goodModel->getContentFromBasket($request);
        $goodModel = $this->newGoodModel();
        $goodModel->createNewOrder($userId, $basket['products']);
        return $goodModel->deleteProductFromBasket(null, $response);
    }


}