<?php

namespace Application\Models;

use Application\TableDataGateway\Filters;
use Application\TableDataGateway\Goods;
use Application\TableDataGateway\Login;
use Application\TableDataGateway\Orders;
use Application\TableDataGateway\Registration;
use Application\TableDataGateway\UserProfile;
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

    /**
     * @return UserProfile
     */
    public function newUserProfile () {
        return new UserProfile($this->app['DbQuery']);
    }

    /**
     * @return Orders
     */
    public function newOrders () {
        return new Orders($this->app['DbQuery']);
    }

    /**
     * @return Filters
     */
    public function newFilters () {
        return new Filters($this->app['DbQuery']);
    }
}