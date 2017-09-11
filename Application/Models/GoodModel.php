<?php

namespace Application\Models;


use Application\Controllers\GoodsController;
use Application\ValueObject\GoodFields;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class GoodModel extends BaseModel
{

    /**
     * @param string $picture
     * @param GoodFields $goodFields
     * @return bool
     */
    public function addGood (string $picture, GoodFields $goodFields) {
        return ($this->newGoods())->addGood($picture, $goodFields);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function savePhoto (UploadedFile $file) {
        $uploaddir = '/home/smelnikova/dev/my_shop.dev/web/pictures';
        $uploadfile = time() . $file->getClientOriginalName();
        $file->move($uploaddir, $uploadfile);
        $filePath = 'pictures/' . $uploadfile;
        return $filePath;
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
     * @return bool
     */
    public function updateProduct (array $product) {
        return ($this->newGoods())->updateProduct($product);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getContentFromBasket (Request $request) {
        return (new GoodsController())->showBasketAction($request);
    }

    /**
     * @param int $userId
     * @param $basket
     */
    public function createNewOrder (int $userId, $basket) {
        $this->newGoods()->createNewOrder($userId, $basket);
    }

    /**
     * @param Request $request
     * @return int
     */
    public function countProducts (Request $request) {
        if (!key_exists('products', ($request->cookies->all()))) {;
            return 0;
        }
        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        $count = 0;
        foreach ($products as $value) {
            $count = $count + $value;
        }
        return $count;
    }

    /**
     * @param int $stokeId
     * @param Response $response
     * @param Request $request
     * @return Response
     */
    public function addProductInBasket (int $stokeId, Response $response, Request $request) {
        $cookie = $request->cookies->all();
        if (key_exists('products', $cookie)) {
            $products = json_decode($cookie['products'], true);

        }
        if (isset($products[$stokeId])) {
            $products[$stokeId] = $products[$stokeId] + 1;
        } else {
            $products[$stokeId] = 1;
        }

        $cookie = new Cookie('products', json_encode($products), strtotime('now + 60 minutes'));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getProductsFromBasket (Request $request) {
        if (key_exists('products', ($request->cookies->all()))) {
            $products = ($request->cookies->all())['products'];
            return json_decode($products, true);
        }
        return [];
    }


    /**
     * @param int|null $stokeId
     * @param Response $response
     * @param Request|null $request
     * @return Response
     */
    public function deleteProductFromBasket (int $stokeId = null, Response $response, Request $request = null) {
        if (empty($stokeId)) {
            $response->headers->clearCookie('products');
        }

        $products = ($request->cookies->all())['products'];
        $products = json_decode($products, true);
        if ($products[$stokeId] > 1) {
            $products[$stokeId] = $products[$stokeId] - 1;
        } else {
            unset($products[$stokeId]);
        }
        $cookie = new Cookie('products', json_encode($products));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
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

}