<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 20.08.17
 * Time: 14:50
 */

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
        $result = $loginModel->isUserExist($login, $password);
        if (!$result) {
            return ['error' => 'Error: This user is not exist. Check out your login and password'];
        }
        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
        $loginModel->createLoginCookie($result, $response);
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