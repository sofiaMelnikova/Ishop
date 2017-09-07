<?php

namespace Application\Controllers;

use Application\Models\LoginModel;
use Application\Models\GoodModel;

class BaseController
{
    /**
     * @return LoginModel
     */
    public function newLoginModel () {
        return new LoginModel();
    }

    /**
     * @return GoodModel
     */
    public function newGoodModel () {
        return new GoodModel();
    }


}