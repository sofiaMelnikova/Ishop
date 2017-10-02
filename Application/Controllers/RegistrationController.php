<?php

namespace Application\Controllers;

use Silex\Application;
use Application\Validate\Validate;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseControllerAbstract
{
    /**
     * @return mixed
     */
    public function renderRegistrationFormAction () {
        return $this->render('registration.php');
    }

    /**
     * @return array|Response
     */
    public function addUserAction() {
        $email = $this->request->request->get('email');
        $passwordHash = password_hash($this->request->request->get('password'), PASSWORD_BCRYPT);
        $phone = $this->request->request->get('phone');

        $validate = new Validate();
        $errors = $validate->registrationFormValidate($this->app, ['email' => $email, 'phone' => $phone]);

        if (!empty($errors)) {
            return $this->render('registration.php', ['errors' => $errors]);
        }

        $userId = $this->app['registration.model']->saveNewUser($email, $phone, $passwordHash);

        return $this->app['login.model']->loginUser(intval($userId), date("Y-m-d H:i:s", strtotime('now + 60 minutes')));
    }

}