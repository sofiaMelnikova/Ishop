<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 05.09.17
 * Time: 16:19
 */

namespace Application\Models;

use Application\Controllers\BaseControllerAbstract;

class Rules extends BaseControllerAbstract
{
    /**
     * @return bool
     */
    public function isLoginAdmin () {
        $loginModel = new LoginModel($this->app);
        $id = $loginModel->isUserLogin($this->request);
        if (!$id || !($loginModel->isAdmin(intval($id)))) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isHaveCsrfToken () {
        if ($this->request->cookies->all()['csrfToken'] === $this->request->request->get('csrfToken')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isLogin () :bool {
        $userId = $this->app['login.model']->isUserLogin($this->request);
        return (!$userId) ? false : true ;
    }
}