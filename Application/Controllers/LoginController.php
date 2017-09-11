<?php

namespace Application\Controllers;

use Application\Models\LoginModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    /**
     * @param Request $request
     * @return array|static
     */
    public function userLoginAction (Request $request) {
        $loginModel = $this->newLoginModel();
        $login = $request->get('login');
        $password = $request->get('password');
        $userId = $loginModel->isUserExist($login, $password);
        if (!$userId) {
            return ['error' => 'Error: This user is not exist. Check out your login and password'];
        }
        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
        $token = $loginModel->createTokenForUser();
        $loginModel->addTokenForUser($token, $userId);
        $response = $loginModel->createLoginCookie($token, $response);
        return $response;
    }

    /**
     * @param Response $response
     * @return Response
     */
    public function logoutAction (Response $response) {
        $response->headers->clearCookie('user');
        return $response;
    }



}