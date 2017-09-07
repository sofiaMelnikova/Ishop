<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
class Login
{
    public $dataBase = null;

    /**
     * LoginModel constructor.
     * @param DbQuery $dataBase
     */
    public function __construct(DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function haveUserCookie (int $userId) {
        $query = "SELECT * FROM `users` WHERE `users`.`id` = :userId;";
        $forExecute = [':userId' => $userId];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param string $login
     * @return array|mixed
     */
    public function isUserExist (string $login) {
        $query = "SELECT * FROM `Ishop`.`users` WHERE `login` = :login AND `is_delete` = 0";
        $forExecute = [':login' => $login];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function isAdmin (int $userId) {
        $query = "SELECT `users`.`admin` FROM `users` WHERE `users`.`id` = :userId;";
        $forExecute = [':userId' => $userId];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getLogin (int $userId) {
        $query = "SELECT `users`.`login` FROM `users` WHERE `users`.`id` = :userId;";
        $forExecute = [':userId' => $userId];
        return $this->dataBase->getData($query, $forExecute, false);
    }

}