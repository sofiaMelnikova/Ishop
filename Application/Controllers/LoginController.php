<?php

namespace Application\Controllers;

use Symfony\Component\HttpFoundation\Response;
// use PHPMailer\PHPMailer\PHPMailer;

class LoginController extends BaseControllerAbstract
{
    /**
     * @return mixed
     */
    public function renderLoginFormActon () {
        $this->addCsrfToken();
         return $this->render('login.php', ['csrfToken' => self::$csrfToken]);
    }

    /**
     * @return Response
     */
    public function userLoginAction () {
        $this->addCsrfToken();
        $login = $this->request->request->get('login');
        $password = $this->request->request->get('password');
        $userId = $this->app['login.model']->isUserExist($login, $password);
        if (!$userId) {
            return $this->render('login.php', ['error' => 'Error: This user is not exist. Check out your login and password',
                'csrfToken' => self::$csrfToken]);
        }
        return $this->app['login.model']->loginUser($userId, date("Y-m-d H:i:s", strtotime('now + 60 minutes')));
    }

    /**
     * @return Response
     */
    public function logoutAction () {
        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/login']);
        $token = $this->request->cookies->all()['user'];
        $this->app['login.model']->sendNowTimeForToken($token);
        $response->headers->clearCookie('user');
        return $response;
    }

    public function showFormRestoringPasswordAction () {
        $this->addCsrfToken();
        return $this->render('restoringPassword.php', ['csrfToken' => self::$csrfToken]);
    }

}