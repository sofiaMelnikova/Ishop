<?php

namespace Application\Models;

use Application\TableDataGateway\Goods;
use Application\TableDataGateway\Login;
use Application\TableDataGateway\Registration;
use Silex\Application;

class BaseModel
{
    private $app;

    /**
     * BaseModel constructor.
     * @param Application $app
     */
    function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * @return Goods
     */
    public function newGoods () {
        return new Goods($this->app['DbQuery']);
    }

    /**
     * @return Login
     */
    public function newLogin () {
        return new Login($this->app['DbQuery']);
    }

    /**
     * @return Registration
     */
    public function newRegistration () {
        return new Registration($this->app['DbQuery']);
    }

    /**
     * @return RegistrationModel
     */
    public function newRegistrationModel () {
        return new RegistrationModel($this->app);
    }
}