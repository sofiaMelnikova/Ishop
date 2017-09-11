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

    /**
     * @param string $token
     * @return array|mixed
     */
    public function getUserIdByToken (string $token) {
        $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`token` = :token;";
        $forExecute = [':token' => $token];
        return $this->dataBase->getData($query, $forExecute, false);
    }

    /**
     * @param string $token
     * @param int $userId
     */
    public function addTokenForUser (string $token, int $userId) {
        $query = "UPDATE `users` SET `users`.`token` = :token WHERE `users`.`id` = :id;";
        $forExecute = [':token' => $token, ':id' => $userId];
        $this->dataBase->changeData($query, $forExecute);
    }

//    /**
//     * @param string $token
//     */
//    public function deleteTokenFromUser (string $token) {
//        $query = "UPDATE `users` SET `users`.`token` = NULL WHERE `users`.`token` = :token;";
//        $forExecute = [':token' => $token];
//        $this->dataBase->changeData($query, $forExecute);
//    }

}