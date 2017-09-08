<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
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
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $passwordHash) {
        $query = "INSERT INTO `Ishop`.`users` (`login`, `password_hash`) VALUES (:login, :passwordHash)";
        $forExecute = ['login' => $login,
            'passwordHash' => $passwordHash];
        $result = $this->dataBase->changeData($query, $forExecute);
        return $result;
    }

    /**
     * @param string $login
     * @return array|mixed
     */
    public function isLoginExist (string $login) {
        $query = "SELECT * FROM `Ishop`.`users` WHERE `login` = :login AND `is_delete` = 0";
        $forExecute = [':login' => $login];
        return $this->dataBase->getData($query, $forExecute, false);

    }

}