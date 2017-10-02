<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 25.09.17
 * Time: 18:35
 */

namespace Application\ValueObject;

use Symfony\Component\HttpFoundation\Request;

class JacketFields
{
    private $stokeId = null;
    private $kind = null;
    private $productName = null;
    private $brand = null;
    private $color =null;
    private $size = null;
    private $material = null;
    private $gender = null;
    private $producer = null;
    private $count = null;
    private $cost = null;

    /**
     * ShoesFields constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $keys = ['stokeId', 'kind', 'productName', 'brand', 'color', 'size', 'material', 'gender', 'producer', 'count' ,'cost'];

        foreach ($keys as $key) {
            $this->$key = $this->forConstruct($request, $key);
        }
    }

    /**
     * @param Request $request
     * @param string $key
     * @return mixed|null
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
            'brand' => $this->brand,
            'color' => $this->color,
            'size' => $this->size,
            'material' => $this->material,
            'gender' => $this->gender,
            'producer' => $this->producer,
            'count' => $this->count,
            'cost' => $this->cost];
    }

    /**
     * @return array
     */
    public function getPropertiesKeys ():array {
        return ['brand', 'color', 'size', 'material', 'gender', 'producer'];
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
    public function getSize () {
        return $this->size;
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
    public function getGender () {
        return $this->gender;
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
    public function getBrand () {
        return $this->brand;
    }

    /**
     * @return null|string
     */
    public function getProducer () {
        return $this->producer;
    }
}