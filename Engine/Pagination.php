<?php

namespace Engine;


use Application\Models\GoodModel;
use Application\Models\LoginModel;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    /**
     * @param int $sumElements
     * @param int $elementsOnPage
     * @return int
     */
    public function getCountPagesOrGroups (int $sumElements, int $elementsOnPage) {

        if ($sumElements <= $elementsOnPage) {
            return 1;
        }

        if (($sumElements%$elementsOnPage) != 0) {
            return intval($sumElements/$elementsOnPage) + 1;
        }

        return $sumElements/$elementsOnPage;
    }

    /**
     * @param int $actualPage
     * @param int $elementsOnPage
     * @return array
     */
    public function getMinMaxElementsOnPage (int $actualPage, int $elementsOnPage) {
        if ($actualPage === 1) {
            return ['min' => 1, 'max' => $elementsOnPage];
        }

        $min = ($actualPage-1)*$elementsOnPage +1;
        $max = $actualPage*$elementsOnPage;
        return ['min' => $min, 'max' => $max];
    }

    /**
     * @param int $actualElement
     * @param int $elementsOnPage
     * @return int
     */
    public function getActualPageByElement (int $actualElement, int $elementsOnPage) {
        $actualPage = 0;
        $find = false;

        while ($find === false) {
            $actualPage++;
            $elements = $this->getMinMaxElementsOnPage($actualPage, $elementsOnPage);

            if ($actualElement >= $elements['min'] && $actualElement <= $elements['max']) {
                $find = true;
            }
        }

        return $actualPage;
    }

    /**
     * @param int $actualPage
     * @param int $countShowPages
     * @return array
     */
    public function getMainMaxPages (int $actualPage, int $countShowPages, int $sumPages) {
        $group = $this->getCountPagesOrGroups($actualPage, $countShowPages);
        $result = $this->getMinMaxElementsOnPage($group, $countShowPages);

        if ($result['max'] > $sumPages) {
            $result['max'] = $sumPages;
        }

        return $result;
    }

    /**
     * @param int $page
     * @param int $productsOnPage
     * @param int $showPages
     * @param Application $app
     * @param Request|null $request
     * @param string|null $kind
     * @param array $filters
     * @return array
     */
    public function showCatalog (int $page, int $productsOnPage, int $showPages, Application $app, Request $request = null, string $kind = null, array $filters = []) {
        $goodModel = new GoodModel($app);

        $countProducts = ($goodModel->isEmptyAllElementsInArray($filters)) ? $goodModel->getCountProducts($kind) : $goodModel->getCountProductsByFilter($kind, $filters);

        $countPages = $this->getCountPagesOrGroups($countProducts, $productsOnPage);

        if ($page > $countPages) {
            $page = $countPages;
        }

        $pagesMinMax = $this->getMainMaxPages($page, $showPages, $countPages);
        $productsMinMax = $this->getMinMaxElementsOnPage($page, $productsOnPage);

        if (is_null($kind)) {
            $products = $goodModel->getPictureNameProduct($productsMinMax['min'], $productsOnPage);
            return ['products' => $products, 'pages' => $pagesMinMax, 'sumPages' => $countPages];
        }

        $products = ($goodModel->isEmptyAllElementsInArray($filters)) ? $goodModel->getNamePicturePriceOfKind($kind, $productsMinMax['min'], $productsOnPage) : $goodModel->getProductsByFilerInLimit($kind, $filters, $productsMinMax['min'], $productsOnPage);

        $loginModel = new LoginModel($app);
        $id = $loginModel->isUserLogin($request);
        $admin = false;
        $login = false;

        if ($id) {
            $countProductsInBasket = $goodModel->countProductsInBasketForLoginUser($id);
            $admin = $loginModel->isAdmin($id);
            $login = $loginModel->getLogin($id);
        } else {
            $countProductsInBasket = $goodModel->countProductsInBasketForLogoutUser($request);
        }

        return ['products' => $products, 'pages' => $pagesMinMax, 'kind' => $kind, 'sumPages' => $countPages,
            'countProductsInBasket' => $countProductsInBasket, 'admin' => $admin, 'login' => $login];
    }

}