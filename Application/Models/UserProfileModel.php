<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 03.10.17
 * Time: 18:25
 */

namespace Application\Models;


class UserProfileModel extends BaseModel
{
    /**
     * @param string $token
     * @return array
     */
    public function getUserIdAvatarFioPhoneLoginByToken (string $token) {
        return $this->newUserProfile()->getUserIdAvatarFioPhoneLoginByToken($token);
    }

    /**
     * @param array $user
     * @param string $avatar
     * @return int
     */
    public function updateUserAvatarFioPhoneLogin (array $user, string $avatar) {
        return $this->newUserProfile()->updateUserAvatarFioPhoneLogin($user, $avatar);
    }
}