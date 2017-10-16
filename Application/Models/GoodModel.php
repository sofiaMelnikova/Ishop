<?php

namespace Application\Models;

use Application\ValueObject\GoodFields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class GoodModel extends BaseModel
{

    /**
     * @param string $picture
     * @param array $property
     * @param array $propertyKeys
     * @return bool
     */
    public function addGood (string $picture, array $property, array $propertyKeys) {
        return ($this->newGoods())->addGood($picture, $property, $propertyKeys);
    }

    /**
     * @param string $kind
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getNamePicturePriceOfKind (string $kind, int $startElement, int $countElements) {
        return ($this->newGoods())->getNamePicturePriceOfKind($kind, $startElement, $countElements);
    }

    /**
     * @param int $idStoke
     * @return array|mixed
     */
    public function getAllOfProduct (int $idStoke) {
        return ($this->newGoods())->getAllOfProduct($idStoke);
    }

    /**
     * @param string|null $kind
     * @return int
     */
    public function getCountProducts (string $kind = null) {
        $result = ($this->newGoods())->getCountProducts($kind);
        return intval(array_shift($result));
    }

    /**
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getPictureNameProduct (int $startElement, int $countElements) {
        return ($this->newGoods())->getPictureNameProduct($startElement, $countElements);
    }

    /**
     * @param int $stokeId
     */
    public function deleteProduct (int $stokeId) {
        ($this->newGoods())->deleteProduct($stokeId);
    }

    /**
     * @param array $product
     * @param array $propertyKeys
     * @return bool
     */
    public function updateProduct (array $product, array $propertyKeys) {
        return ($this->newGoods())->updateProduct($product, $propertyKeys);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getContentForShowingBasketForLogoutUser (Request $request) {
        $result = $this->getContentForShowingBasket($this->getProductsFromBasketForLogoutUser($request));
        $result['logout'] = true;
        return $result;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getContentForShowingBasketForLoginUser (int $userId) {
        $result = $this->getContentForShowingBasket($this->getProductsFromBasketForLoginUser($userId));
        $result['logout'] = false;
        return $result;
    }

    /**
     * @param array $basketProducts
     * @return array
     */
    private function getContentForShowingBasket (array $basketProducts) {
        $resultSum = 0;
        $products = [];

        foreach ($basketProducts as $key => $value) {
            $product = $this->getAllOfProduct($key);
            $resultSum = $resultSum + $value['sum'];
            $product = array_merge($product, ['countInBasket' => $value['count'], 'sum' => $value['sum']]);
            array_push($products, $product);
        }

        return ['products' => $products,'resultSum' => $resultSum];
    }


    /**
     * @param int $numberOrder
     */
    public function executedOrderForLoginUser (int $numberOrder) {
        $this->newOrders()->formBusketToExecuted($numberOrder);
    }

    /**
     * @param int $userId
     * @param array $basket
     */
    public function executedOrderForLogoutUser (int $userId, array $basket) {
//        $this->newGoods()->createAndExecuteNewOrder($userId, $basket);
        $this->newOrders()->createAndExecuteNewOrder($userId, $basket);
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getNumberOrdesInBasketForUser (int $userId) {
        $numberOrder = $this->newOrders()->getNumberOrdesInBasketForUser($userId);

        if (empty($numberOrder)) {
            return 0;
        }

        return intval($numberOrder['id']);
    }

    /**
     * @param Request $request
     * @return int
     */
    public function countProductsInBasketForLogoutUser (Request $request) {
        if (!key_exists('products', ($request->cookies->all()))) {;
            return 0;
        }

        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        $count = 0;

        foreach ($products as $value) {
            $count = ++$count;
        }
        return $count;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function countProductsInBasketForLoginUser (int $userId) {
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);

        if (empty($numberOrder)) {
            return 0;
        }

        $countProducts = $this->newOrders()->getCountProductsInBasket($numberOrder);

        return intval($countProducts['COUNT(*)']);
    }

    /**
     * @param int $userId
     * @param array $product
     */
    public function addProductInBasketForLoginUser (int $userId, array $product) {
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);

        if (empty($numberOrder)) {
//            $this->newGoods()->createNewOrder($userId, $product);
            $this->newOrders()->createNewOrder($userId, $product);

        } else {
            $this->newOrders()->addToOrderBasketProduct($numberOrder, $product);
        }
    }

    /**
     * @param Response $response
     * @param Request $request
     * @param array $product
     * @return Response
     */
    public function addProductInBasketForLogoutUser (Response $response, Request $request, array $product) {
        $products = [];
        $cookie = $request->cookies->all();

        if (key_exists('products', $cookie)) {
            $products = json_decode($cookie['products'], true);
        }

        array_push($products, ['cost' => $product['cost'], 'id' => $product['id']]);
        $cookie = new Cookie('products', json_encode($products));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param Request $request
     * @param bool $forShowBasket
     * @return array|mixed
     */
    public function getProductsFromBasketForLogoutUser (Request $request, bool $forShowBasket = true) {
        if (!key_exists('products', ($request->cookies->all()))) {
            return [];
        }

        if (!$forShowBasket) {
            $products = ($request->cookies->all())['products'];
            return json_decode($products, true);
        }

        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        $result = [];

        foreach ($products as $key => $value) {
            if (array_key_exists($value['id'], $result)) {
                ++$result[$value['id']]['count'];
                $result[$value['id']]['sum'] += $value['cost'];
            } else {
                $result[$value['id']] = ['count' => 1, 'sum' => $value['cost']];
            }
        }

        return $result;
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getProductsFromBasketForLoginUser (int $userId) {
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);

        if (empty($numberOrder)) {
            return [];
        }

        $products = $this->newOrders()->getProductsFromBasket($numberOrder);

        if (empty($products)) {
            return [];
        }

        $result = [];

        foreach ($products as $key => $value) {
            if (array_key_exists($value['stoke_id'], $result)) {
                ++$result[$value['stoke_id']]['count'];
                $result[$value['stoke_id']]['sum'] += $value['actual_cost'];
            } else {
                $result[$value['stoke_id']] = ['count' => 1, 'sum' => $value['actual_cost']];
            }
        }

        return $result;
    }


    /**
     * @param int|null $stokeId
     * @param Response $response
     * @param Request|null $request
     * @return Response
     */
    public function deleteProductFromBasketForLogoutUser (Response $response, int $stokeId = null, Request $request = null) {
        if (empty($stokeId)) {
            $response->headers->clearCookie('products');
            return $response;
        }

        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);

        foreach ($products as $key => $value) {
            if ($value['id'] == $stokeId) {
                unset($products[$key]);
                $cookie = new Cookie('products', json_encode($products));
                $response->headers->setCookie($cookie);
                return $response;
            }
        }
    }

    /**
     * @param int $userId
     * @param int|null $stokeId
     */
    public function deleteProductFromBasketForLoginUser (int $userId, int $stokeId = null) {
        $numberOrder = $this->getNumberOrdesInBasketForUser($userId);
        $this->newOrders()->deleteFromBasket($numberOrder, $stokeId);
    }

    /**
     * @param string $kind
     * @return array|null
     */
    public function getFieldsByKindForAddForm (string $kind) {
        $properties = null;

        if ($kind === 'shoes') {
            $properties = ['size' => ['min' => 36, 'max' => 46], 'brand' => true, 'gender' => true, 'color' => true,
                'material' => true, 'producer' => true, 'kind' => 'shoes'];
        }

        if ($kind === 'jacket') {
            $properties = ['size' => ['min' => 38, 'max' => 56], 'brand' => true, 'gender' =>true, 'color' => true,
                'material' => true, 'producer' => true, 'kind' => 'jacket'];
        }

        if ($kind === 'plaid') {
            $properties = ['length' => true, 'width' => true, 'color' => true, 'material' => true, 'producer' => true, 'kind' => 'plaid'];
        }

        return $properties;
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getInfoForHistoryOrdersByUserId (int $userId) {
        return $this->newOrders()->getInfoForHistoryOrdersByUserId($userId);
    }

    /**
     * @param string $phoneNumber
     * @return array
     */
    public function getHistoryForLogoutUser (string $phoneNumber) {
        $userId = $this->newRegistrationModel()->getUserByPhone($phoneNumber);

        if (!$userId) {
            return ['error' => 'You have not orders.'];
        }

        $orders = $this->getInfoForHistoryOrdersByUserId($userId);

        if (empty($orders)) {
            return ['error' => 'You have not orders.'];
        }

        return ['products' => $orders];
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getHistoryForLoginUser (int $userId) {
        return ['products' => $this->getInfoForHistoryOrdersByUserId($userId)];
    }

    /**
     * @param array $filters
     * @return array
     */
    // ['namePropertyInBD' => ['value' => ['min' => , 'max' => ]/'value', 'type' => ]]
    private function getShoesSortByFilter (array $filters) {
        return ['cost' => ['value' => ['min' => trim($filters['minCost']), 'max' => trim($filters['maxCost'])], 'type' => \PDO::PARAM_INT],
            'brand' => ['value' => trim($filters['brand']), 'type' => \PDO::PARAM_STR],
            'color' => ['value' => trim($filters['color']), 'type' => \PDO::PARAM_STR],
            'size' => ['value' => trim($filters['size']), 'type' => \PDO::PARAM_INT],
            'gender' => ['value' => trim($filters['gender']), 'type' => \PDO::PARAM_STR]];
    }

    /**
     * @param array $filters
     * @return array
     */
    private function getJacketSortByFilter (array $filters) {
        return ['cost' => ['value' => ['min' => trim($filters['minCost']), 'max' => trim($filters['maxCost'])], 'type' => \PDO::PARAM_INT],
            'brand' => ['value' => trim($filters['brand']), 'type' => \PDO::PARAM_STR],
            'color' => ['value' => trim($filters['color']), 'type' => \PDO::PARAM_STR],
            'size' => ['value' => trim($filters['size']), 'type' => \PDO::PARAM_INT],
            'gender' => ['value' => trim($filters['gender']), 'type' => \PDO::PARAM_STR]];

    }

    /**
     * @param array $filters
     * @return array
     */
    private function getPlaidSortByFilter (array $filters) {
        return ['cost' => ['value' => ['min' => trim($filters['minCost']), 'max' => trim($filters['maxCost'])], 'type' => \PDO::PARAM_INT],
            'brand' => ['value' => trim($filters['brand']), 'type' => \PDO::PARAM_STR],
            'length' => ['value' => ['min' => trim($filters['minLength']), 'max' => trim($filters['maxLength'])], 'type' => \PDO::PARAM_INT],
            'width' => ['value' => ['min' => trim($filters['minWidth']), 'max' => trim($filters['maxWidth'])], 'type' => \PDO::PARAM_INT]];
    }

    /**
     * @param string $kind
     * @param array $filters
     * @return int
     */
    public function getCountProductsByFilter (string $kind, array $filters) {
        $methodName = 'get' . ucfirst($kind) . 'SortByFilter';
        return intval($this->newFilters()->getCountProductsByFilters($kind, $this->$methodName($filters))['COUNT(*)']);
    }

    /**
     * @param string $kind
     * @param array $filters
     * @param int $startElement
     * @param int $countElements
     * @return array
     */
    public function getProductsByFilerInLimit (string $kind, array $filters, int $startElement, int $countElements) {
        $methodName = 'get' . ucfirst($kind) . 'SortByFilter';
        return $this->newFilters()->getProductsByFiltersInLimit($kind, $this->$methodName($filters), $startElement, $countElements);
    }

    /**
     * @param array $elements
     * @return bool
     */
    public function isEmptyAllElementsInArray (array $elements) {
        $result = true;

        foreach ($elements as $value) {
            $result = (empty($value)) ? $result : false;
        }

        return $result;
    }
}