<?php

namespace Application\Models;

use Engine\DbQuery;
use Application\TableDataGateway\Registration;
use Application\Validate\Validate;
use Silex\Application;

class RegistrationModel extends BaseModel
{
    /**
     * @param string $login
     * @param string $phone
     * @param string $passwordHash
     * @return bool|string
     */
    public function saveNewUser (string $login, string $phone, string $passwordHash) {
        return $this->newRegistration()->saveNewUser($login, $phone, $passwordHash);
    }


    /**
     * @param string $login
     * @return bool
     */
    public function isLoginExist (string $login) {
        $result = $this->newRegistration()->isLoginExist($login);
        if (empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function getUserByPhone (string $phoneNumber):int {
        $userId = $this->newRegistration()->getUserByPhone($phoneNumber);
        if (empty($userId)) {
            return 0;
        }
        return intval($userId['id']);
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function getUnregisteredUserByPhone (string $phoneNumber) {
        $userId = $this->newRegistration()->getUnregisteredUserByPhone($phoneNumber);
        if (empty($userId)) {
            return 0;
        }
        return intval($userId['id']);
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function getRegistretedUserByPhone (string $phoneNumber) {
        $userId = $this->newRegistration()->getRegistretedUserByPhone($phoneNumber);
        if (empty($userId)) {
            return 0;
        }
        return intval($userId['id']);
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function addNewUserByPhone (string  $phoneNumber) {
        $userId = $this->newRegistration()->addNewUserByPhone($phoneNumber);
        return intval($userId);
    }

    /**
     * @param Application $app
     * @param string $phoneNumber
     * @return array|bool|int
     */
    public function registrateNewUserByPhone (Application $app, string $phoneNumber) {
        $validate = new Validate();
        $errors = $validate->phoneValidate($app, ['phone' => $phoneNumber]);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $userId = $this->getUserByPhone($phoneNumber);
        if ($userId) {
            return $userId;
        }
        return $this->addNewUserByPhone($phoneNumber);
    }

    /**
     * @param array $user
     * @return mixed
     */
    public function updateLoginPasswordPhone (array $user) {
        return $this->newRegistration()->updateLoginPasswordPhone($user);
    }

}