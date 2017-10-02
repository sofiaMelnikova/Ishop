<?php

namespace Application\Controllers;

use Silex\Application;
use Application\Validate\Validate;
use Engine\Pagination;
use Symfony\Component\HttpFoundation\Response;

class GoodsAdminController extends BaseControllerAbstract
{
    private $productsOnPageAdmin = 4;
    private $showPagesAdmin = 3;

    /**
     * @return Response
     */
    public function showFormAddGood () {
        $kind = $this->request->query->get('kind');
        return $this->render('add' . ucfirst($kind) . '.php');
    }

    /**
     * @return Response
     */
    public function addGoodAction () {
        $kind = $this->request->request->get('kind');
        $validate = new Validate();
        $productFields = $this->app[$kind . '.fields'];
        $methodValidate = ($kind . 'FormValidate');
        $result = $validate->$methodValidate($this->app, $productFields->getAllFields());

        if (!empty($result)) {
            return $this->render('add' . ucfirst($kind) . '.php', ['product' => $productFields->getAllFields(), 'error' => $result]);
        }

        $file = $this->request->files->get('photo');
        $filePath = 'pictures/addPhoto.png';

        if (!empty($file)) {
            $errors = $validate->imageValidate($this->app, ['picture' => $file]);

            if (!empty($errors)) {
                return $this->render('add' . ucfirst($kind) . '.php', ['product' => $productFields->getAllFields(), 'error' => $errors]);
            }
            $filePath = 'pictures/' . ($this->app['uploader.helper']->upload($file, '/home/smelnikova/dev/my_shop.dev/web/pictures'));
        }

        $this->app['good.model']->addGood($filePath, $productFields->getAllFields(), $productFields->getPropertiesKeys());
        return Response::create('', 302, ['Location' => 'http://127.0.0.1/adminGoods']);
    }

    /**
     * @param int $page
     * @return mixed
     */
    public function showAdminGoodsAction (int $page) {
        $result = (new Pagination())->showCatalog($page, $this->productsOnPageAdmin, $this->showPagesAdmin, $this->app);
        return $this->render('adminGoods.php', $result);
    }

    /**
     * @return Response
     */
    public function changeProductAction () {
        $stokeId = intval($this->request->query->get('id'));
        $product = $this->app['good.model']->getAllOfProduct($stokeId);
        return $this->render('add' . ucfirst($product['kinds_value']) . '.php', ['product' => $product, 'editProduct' => true]);
    }

    /**
     * @return Response;
     */
    public function deleteProductAction () {
        $stokeId = intval($this->request->request->get('id'));
        $this->app['good.model']->deleteProduct($stokeId);
        return Response::create('', 302, ['Location' => 'http://127.0.0.1/adminGoods']);
    }

    /**
     * @return Response
     */
    public function saveChangeProductAction () {
        $kind = $this->request->request->get('kind');
        $validate = new Validate();

        $productFields = $this->app[$kind . '.fields'];
        $methodValidate = ($kind . 'FormValidate');
        $result = $validate->$methodValidate($this->app, $productFields->getAllFields());

        if (!empty($result)) {

            $result = ['error' => $result,
                'product' => $this->app['good.model']->getAllOfProduct(intval($productFields->getStokeId())),
                'editProduct' => true];

            return $this->render('add' . ucfirst($kind) . '.php', $result);
        }

        $file = $this->request->files->get('photo');
        $filePath = '';

        if (!empty($file)) {
            $errors = $validate->imageValidate($this->app, ['picture' => $file]);
            if (!empty($errors)) {
                return $this->render('add' . ucfirst($kind) . '.php', ['product' => $productFields->getAllFields(), 'error' => $errors]);
            }
            $filePath = 'pictures/' . ($this->app['uploader.helper']->upload($file, '/home/smelnikova/dev/my_shop.dev/web/pictures'));
        }

        $product = array_merge($productFields->getAllFields(), ['picture' => $filePath]);
        $this->app['good.model']->updateProduct($product, $productFields->getPropertiesKeys());
        $result = ['product' => $this->app['good.model']->getAllOfProduct(intval($productFields->getStokeId())), 'editProduct' => true];
        return $this->render('add' . ucfirst($kind) . '.php', $result);
    }


}