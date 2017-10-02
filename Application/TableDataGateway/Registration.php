<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
use PDO;
class Registration
{
    private $dataBase = null;

    /**
     * RegistrationController constructor.
     * @param DbQuery $dataBase
     */
    public function __construct (DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param string $login
     * @param string $phone
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $phone, string $passwordHash) {
        $query = "INSERT INTO `Ishop`.`users` (`login`, `phone`, `password_hash`) VALUES (:login, :phone, :passwordHash)";
        $params = [':login' => ['value' => $login, 'type' => PDO::PARAM_STR],
            ':phone' => ['value' => $phone, 'type' => PDO::PARAM_STR],
            ':passwordHash' => ['value' => $passwordHash, 'type' => PDO::PARAM_STR]];
        $result = $this->dataBase->insert($query, $params);
        return $result;
    }

    /**
     * @param string $login
     * @return array|mixed
     */
    public function isLoginExist (string $login) {
        $query = "SELECT * FROM `Ishop`.`users` WHERE `login` = :login AND `is_delete` = 0";
        $params = [':login' => ['value' => $login, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params, false);

    }

    /**
     * @param string $phoneNumber
     * @return array|mixed
     */
    public function getUserByPhone (string $phoneNumber) {
        $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`phone` = :phoneNumber";
        $params = [':phoneNumber' => ['value' => $phoneNumber, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param string $phoneNumber
     * @return bool|string
     */
    public function addNewUserByPhone (string $phoneNumber) {
        $query = "INSERT INTO `users` (`phone`) VALUES (:phoneNumber)";
        $params = [':phoneNumber' => ['value' => $phoneNumber, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->insert($query, $params);
    }
}