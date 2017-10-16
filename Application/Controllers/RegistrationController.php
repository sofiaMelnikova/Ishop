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
        $this->addCsrfToken();
        return $this->render('registration.php', ['csrfToken' => self::$csrfToken]);
    }

    /**
     * @return array|Response
     */
    public function addUserAction() {
        $this->addCsrfToken();
        $email = $this->request->request->get('email');
        $passwordHash = password_hash($this->request->request->get('password'), PASSWORD_BCRYPT);
        $phone = $this->request->request->get('phone');

        $validate = new Validate();
        $errors = $validate->registrationFormValidate($this->app, ['email' => $email, 'phone' => $phone]);

        if (!empty($errors)) {
            return $this->render('registration.php', ['errors' => $errors, 'csrfToken' => self::$csrfToken]);
        }

        $userId = $this->app['registration.model']->getUnregisteredUserByPhone($phone);

        if (!empty($userId)) {
            $this->app['registration.model']->updateLoginPasswordPhone(['id' => $userId,'email' => $email, 'phone' => $phone, 'passwordHash' => $passwordHash]);
        } else {
            $userId = $this->app['registration.model']->saveNewUser($email, $phone, $passwordHash);
        }

        return $this->app['login.model']->loginUser(intval($userId), date("Y-m-d H:i:s", strtotime('now + 60 minutes')));
    }

}