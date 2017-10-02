<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
use PDO;
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
        $params = [':login' => ['value' => $login, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function isAdmin (int $userId) {
        $query = "SELECT `users`.`admin` FROM `users` WHERE `users`.`id` = :userId;";
        $params = [':userId' => ['value' => $userId, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getLogin (int $userId) {
        $query = "SELECT `users`.`login` FROM `users` WHERE `users`.`id` = :userId;";
        $params = [':userId' => ['value' => $userId, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param string $token
     * @return array|mixed
     */
    public function getUserIdByToken (string $token) {
        $now = date("Y-m-d H:i:s", strtotime('now'));
        $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`token` = :token AND `users`.`token_end` > :now;";
        $params = [':token' => ['value' => $token, 'type' => PDO::PARAM_STR], ':now' => ['value' => $now, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param string $token
     * @param string $endTimeToken
     * @param int $userId
     */
    public function addTokenForUser (string $token, string $endTimeToken, int $userId) {
        $query = "UPDATE `users` SET `users`.`token` = :token, `users`.`token_end` = :endTime WHERE `users`.`id` = :id;";
        $params = [':token' => ['value' => $token, 'type' => PDO::PARAM_STR],
            ':endTime' => ['value' => $endTimeToken, 'type' => PDO::PARAM_STR],
            ':id' => ['value' => $userId, 'type' => PDO::PARAM_INT]];
        $this->dataBase->update($query, $params);
    }

    /**
     * @param string $token
     * @param string $time
     */
    public function updateTimeForToken (string $token, string $time) {
        $query = "UPDATE `users` SET `users`.`token_end` = :newTime WHERE `users`.`token` = :token;";
        $params = [':newTime' => ['value' => $time, 'type' => PDO::PARAM_STR],
            ':token' => ['value' => $token, 'type' => PDO::PARAM_STR]];
        $this->dataBase->update($query, $params);
    }

}