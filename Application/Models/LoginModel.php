<?php

namespace Application\Models;

use Application\Helpers\RandomString;
use Engine\DbQuery;
use Application\TableDataGateway\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Helpers\Helper;

class LoginModel extends BaseModel
{
    /**
     * @param string $token
     * @return bool|int
     */
    public function getUserIdByToket (string $token) {
        $result = $this->newLogin()->getUserIdByToken($token);
        if ($result) {
            return intval($result['id']);
        }
        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool|int
     */
    public function isUserExist (string $login, string $password) {
        $user = $this->newLogin()->isUserExist($login);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            return intval($user['id']);
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool|int
     */
    public function isUserLogin (Request $request):int {
        if (!key_exists('user', ($request->cookies->all()))) {
            return false;
        }
        $token = ($request->cookies->all())['user'];
        $userId = $this->getUserIdByToket($token);
        if ($userId) {
            return $userId;
        }
        return false;
    }

    /**
     * @param int $useId
     * @return bool
     */
    public function isAdmin (int $useId) {
        $admin = $this->newLogin()->isAdmin($useId);
        $admin = array_shift($admin);
        if ($admin === '1') {
            return true;
        }
        return false;
    }

    /**
     * @param string $token
     * @param Response $response
     * @return Response
     */
    public function createLoginCookie(string $token, Response $response) {
        $cookie = new \Symfony\Component\HttpFoundation\Cookie('user', $token, strtotime('now + 60 minutes'));
        $response->headers->setCookie($cookie);
        $response->send();
        return $response;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getLogin (int $userId) {
        $result = $this->newLogin()->getLogin($userId);
        return $result['login'];
    }

    /**
     * @return bool|string
     */
    public function createTokenForUser () {
        $time = 1;
        while ($time <= 3) {
            $randomString = (new RandomString())::get();
            if (!($this->newLogin()->getUserIdByToken($randomString))) {
                return $randomString;
            }
            $time++;
        }
        return false;
    }

    /**
     * @param string $token
     * @param string $endTokenTime
     * @param int $userId
     */
    public function addTokenForUser (string $token, string $endTokenTime, int $userId) {
        $this->newLogin()->addTokenForUser($token, $endTokenTime, $userId);
    }

    /**
     * @param string $token
     */
    public function sendNowTimeForToken (string $token) {
        $nowTime = date("Y-m-d H:i:s", strtotime("now"));
        $this->newLogin()->updateTimeForToken($token, $nowTime);
    }

    /**
     * @param int $userId
     * @param string $endTime
     * @return Response
     */
    public function loginUser (int $userId, string $endTime) {
        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
        $token = $this->createTokenForUser();
        $this->addTokenForUser($token, $endTime, $userId);
        return $this->createLoginCookie($token, $response);
    }

    public function restoringPassword (string $email) {

    }
}