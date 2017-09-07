<?php

namespace Application\Models;

use Engine\DbQuery;
use Application\TableDataGateway\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class LoginModel
{
    /**
     * @param DbQuery $dbQuery
     * @return Login
     */
    public function newLogin (DbQuery $dbQuery) {
        return new Login($dbQuery);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function haveUserCookie (int $userId) {
        $result = ($this->newLogin(new DbQuery()))->haveUserCookie($userId);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function isUserExist (string $login, string $password) {
        $user = ($this->newLogin(new DbQuery()))->isUserExist($login);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            return $user['id'];
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool|int
     */
    public function isUserLogin (Request $request) {
        if (!key_exists('user', ($request->cookies->all()))) {
            return false;
        }
        $userId = intval(($request->cookies->all())['user']);
        $result = $this->haveUserCookie($userId);
        if ($result) {
            return $userId;
        }
        return false;
    }

    /**
     * @param int $useId
     * @return bool
     */
    public function isAdmin (int $useId) {
        $admin = ($this->newLogin(new DbQuery()))->isAdmin($useId);
        $admin = array_shift($admin);
        if ($admin === '1') {
            return true;
        }
        return false;
    }

    /**
     * @param string $userId
     * @param Response $response
     * @return Response
     */
    public function createLoginCookie(string $userId, Response $response) {
        $cookie = new \Symfony\Component\HttpFoundation\Cookie('user', $userId, strtotime('now + 60 minutes'));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getLogin (int $userId) {
        $result = ($this->newLogin(new DbQuery))->getLogin($userId);
        return $result['login'];
    }
}