<?php

namespace Application\Models;

use Engine\DbQuery;
use Application\TableDataGateway\Registration;
class RegistrationModel
{
    /**
     * @param DbQuery $dbQuery
     * @return Registration
     */
    public function newRegistration (DbQuery $dbQuery) {
        return new Registration($dbQuery);
    }

    /**
     * @param string $login
     * @param string $phone
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $phone, string $passwordHash) {
        return ($this->newRegistration(new DbQuery()))->saveNewUser($login, $phone, $passwordHash);
    }


    /**
     * @param string $login
     * @return bool
     */
    public function isLoginExist (string $login) {
        $result = ($this->newRegistration(new DbQuery()))->isLoginExist($login);
        if (empty($result)) {
            return false;
        }
        return true;
    }




}