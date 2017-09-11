<?php

namespace Application\Controllers;

use Application\Models\LoginModel;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Application\Models\RegistrationModel;
use Engine\DbQuery;
use Engine\Validate;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController
{
    private $app = null;

    /**
     * RegistrationController constructor.
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * @return RegistrationModel
     */
    public  function newRegistrationMode() {
        return new RegistrationModel();
    }


    /**
     * @param Application $app
     * @param Request $request
     * @return array|static
     */
    public function addUserAction(Application $app, Request $request) {
        $loginModel = new LoginModel();

        $email = $request->get('email');
        $passwordHash = password_hash($request->get('password'), PASSWORD_BCRYPT);

        $registrationModel = $this->newRegistrationMode();
        $isUserExist = $registrationModel->isLoginExist($email);
        if ($isUserExist) {
            return ['error' => 'Error: User already exist whith this login.'];
        }

        $validate = new Validate();
        $result = $validate->isEmailValid($app, $email);

        if (!$result) {
            return ['error' => 'Error: Login is not corrected.'];
        }

        $registrationModel = new RegistrationModel(new DbQuery());
        $userId = $registrationModel->saveNewUser($email, $passwordHash);
        if ($userId === false) {
            return ['error' => 'Error: new user was not created.'];
        }

        $response = Response::create('', 302, ['Location' => 'http://127.0.0.1/catalogue']);
        $token = $loginModel->createTokenForUser();
        $loginModel->addTokenForUser($token, $userId);
        $response = $loginModel->createLoginCookie($token, $response);
        return $response;
    }

}