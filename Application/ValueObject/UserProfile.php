<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 04.10.17
 * Time: 9:52
 */

namespace Application\ValueObject;

use Symfony\Component\HttpFoundation\Request;

class UserProfile
{
    private $id = null;
    private $login =null;
    private $fio = null;
    private $phone =null;

    public function __construct (Request $request) {
        foreach (['id', 'login', 'fio', 'phone'] as $key) {
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
        if ($request->request->has($key)) {
            $product = $request->request->get($key);
            if (empty($product)) {
                return null;
            }
            return $product;
        }
        return '';
    }

    /**
     * @return array
     */
    public function getAllFields () {
        return ['id' => $this->id, 'login' => $this->login, 'fio' => $this->fio, 'phone' => $this->phone];
    }

    /**
     * @return mixed
     */
    public function getId () {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLogin () {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getFio () {
        return $this->fio;
    }

    /**
     * @return mixed
     */
    public function getPhone () {
        return $this->phone;
    }
}