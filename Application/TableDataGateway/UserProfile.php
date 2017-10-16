<?php
namespace Application\TableDataGateway;


use Engine\DbQuery;

class UserProfile
{
    /**
     * @var DbQuery|null
     */
    private $dataBase = null;

    /**
     * UserProfile constructor.
     * @param DbQuery $dbQuery
     */
    public function __construct(DbQuery $dbQuery) {
        $this->dataBase = $dbQuery;
    }

    /**
     * @param string $token
     * @return array
     */
    public function getUserIdAvatarFioPhoneLoginByToken (string $token) {
        $query = "SELECT `users`.`id`, `users`.`login`, `users`.`avatar`, `users`.`fio`, `users`.`phone` 
                  FROM `users` WHERE `users`.`token` = :token";
        $params = [':token' => ['value' => $token, 'type' => \PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params, false);
    }

    /**
     * @param array $user
     * @param string $avatar
     * @return int
     */
    public function updateUserAvatarFioPhoneLogin (array $user, string $avatar) {
        $query = "UPDATE `users` SET `users`.`login` = :login, `users`.`avatar` = :avatar, `users`.`fio` = :fio, 
                  `users`.`phone` = :phone WHERE `users`.`id` = :id";
        $params = [':login' => ['value' => $user['login'], 'type' => \PDO::PARAM_STR],
            ':avatar' => ['value' => $avatar, 'type' => \PDO::PARAM_STR],
            ':fio' => ['value' => $user['fio'], 'type' => \PDO::PARAM_STR],
            ':phone' => ['value' => $user['phone'], 'type' => \PDO::PARAM_STR],
            ':id' => ['value' => $user['id'], 'type' => \PDO::PARAM_INT]];
        return $this->dataBase->update($query, $params);
    }

}