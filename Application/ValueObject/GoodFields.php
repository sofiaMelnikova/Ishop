<?php

namespace Application\ValueObject;


use Symfony\Component\HttpFoundation\Request;

class GoodFields
{
    private $stokeId = null;
    private $kind = null;
    private $productName = null;
    private $brand = null; // new
    private $color =null;
    private $size = null;
    private $material = null;
    private $gender = null;
    private $length = null; // new
    private $width = null; // new
    private $producer = null; // new
    private $count = null;
    private $cost = null;

//    /**
//     * GoodFields constructor.
//     * @param Request $request
//     */
//    public function __construct(Request $request) {
//        $this->stokeId = $request->get('id');
//        $this->kind = $request->get('kind');
//        $this->productName = $request->get('productName');
//        $this->brand = $request->get('brand');
//        $this->color = $request->get('color');
//        $this->size = $request->get('size');
//        $this->material = $request->get('material');
//        $this->gender = $request->get('gender');
//        $this->length = $request->get('length');
//        $this->width = $request->get('width');
//        $this->producer = $request->get('producer');
//        $this->count = $request->get('count');
//        $this->cost = $request->get('cost');
//    }

//    /**
//     * GoodFields constructor.
//     * @param Request $request
//     */
//    public function __construct(Request $request) {
//        $this->stokeId = $this->forConstruct($request->get('id'));
//        $this->kind = $this->forConstruct($request->get('kind'));
//        $this->productName = $this->forConstruct($request->get('productName'));
//        $this->brand = $this->forConstruct($request->get('brand'));
//        $this->color = $this->forConstruct($request->get('color'));
//        $this->size = $this->forConstruct($request->get('size'));
//        $this->material = $this->forConstruct($request->get('material'));
//        $this->gender = $this->forConstruct($request->get('gender'));
//        $this->length = $this->forConstruct($request->get('length'));
//        $this->width = $this->forConstruct($request->get('width'));
//        $this->producer = $this->forConstruct($request->get('producer'));
//        $this->count = $this->forConstruct($request->get('count'));
//        $this->cost = $this->forConstruct($request->get('cost'));
//    }


    /**
     * GoodFields constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->stokeId = $this->forConstruct($request, 'id');
        $this->kind = $this->forConstruct($request, 'kind');
        $this->productName = $this->forConstruct($request,'productName');
        $this->brand = $this->forConstruct($request, 'brand');
        $this->color = $this->forConstruct($request, 'color');
        $this->size = $this->forConstruct($request, 'size');
        $this->material = $this->forConstruct($request, 'material');
        $this->gender = $this->forConstruct($request, 'gender');
        $this->length = $this->forConstruct($request, 'length');
        $this->width = $this->forConstruct($request, 'width');
        $this->producer = $this->forConstruct($request, 'producer');
        $this->count = $this->forConstruct($request, 'count');
        $this->cost = $this->forConstruct($request, 'cost');
    }

    /**
     * @param Request $request
     * @param string $key
     * @return mixed|null|string
     *
     */
    private function forConstruct (Request $request, string $key) {
        $property = $request->get($key);
        if (empty($property)) {
            return null;
        }
        return $property;
    }

    /**
     * @return array
     */
    public  function getAllfields () {
        return ['id' => $this->stokeId,
            'kind' => $this->kind,
            'productName' => $this->productName,
            'brand' => $this->brand,
            'color' => $this->color,
            'size' => $this->size,
            'material' => $this->material,
            'gender' => $this->gender,
            'length' => $this->length,
            'width' => $this->width,
            'producer' => $this->producer,
            'count' => $this->count,
            'cost' => $this->cost];
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
    public function getLength () {
        return $this->length;
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