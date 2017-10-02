<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 25.09.17
 * Time: 18:36
 */

namespace Application\ValueObject;

use Symfony\Component\HttpFoundation\Request;

class PlaidFields
{
    private $stokeId = null;
    private $kind = null;
    private $productName = null;
    private $color =null;
    private $length = null;
    private $width = null;
    private $material = null;
    private $producer = null;
    private $count = null;
    private $cost = null;

    /**
     * GoodFields constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $keys = ['stokeId', 'kind', 'productName', 'color', 'length', 'width', 'material', 'producer', 'count' ,'cost'];
        foreach ($keys as $key) {
            $this->$key = $this->forConstruct($request, $key);
        }
    }

    /**
     * @param Request $request
     * @param string $key
     * @return mixed|null|string
     *
     */
    private function forConstruct (Request $request, string $key) {
        $product = $request->request->get($key);
        if (empty($product)) {
            return null;
        }
        return $product;
    }

    /**
     * @return array
     */
    public  function getAllFields () {
        return ['stokeId' => $this->stokeId,
            'kind' => $this->kind,
            'productName' => $this->productName,
            'color' => $this->color,
            'material' => $this->material,
            'length' => $this->length,
            'width' => $this->width,
            'producer' => $this->producer,
            'count' => $this->count,
            'cost' => $this->cost];
    }

    /**
     * @return array
     */
    public function getPropertiesKeys ():array {
        return ['color', 'material', 'length', 'width', 'producer'];
    }

    /**
     * @return null|string
     */
    public function getKind () {
        return $this->kind;
    }

    /**
     * @return null|string
     */
    public function getProductName () {
        return $this->productName;
    }

    /**
     * @return null|string
     */
    public function getColor () {
        return $this->color;
    }

    /**
     * @return null|string
     */
    public function getMaterial () {
        return $this->material;
    }

    /**
     * @return null|string
     */
    public function getCount () {
        return $this->count;
    }

    /**
     * @return null|string
     */
    public function getCost () {
        return $this->cost;
    }

    /**
     * @return null|string
     */
    public function getStokeId () {
        return $this->stokeId;
    }

    /**
     * @return null|string
     */
    public function getLength () {
        return $this->length;
    }

    /**
     * @return null|string
     */
    public function getWidth () {
        return $this->width;
    }

    /**
     * @return null|string
     */
    public function getProducer () {
        return $this->producer;
    }
}