<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 03.10.17
 * Time: 18:24
 */

namespace Application\Controllers;


use Application\Models\UserProfileModel;
use Application\Validate\Validate;
use Application\ValueObject\UserProfile;

class UserProfileController extends BaseControllerAbstract
{
    /**
     * @return UserProfileModel
     */
    public function newUserProfileModel () {
        return new UserProfileModel($this->app);
    }

    /**
     * @return UserProfile
     */
    public function newUserProfile () {
        return new UserProfile($this->request);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserProfileAction () {
        $result = $this->newUserProfileModel()->getUserIdAvatarFioPhoneLoginByToken(($this->request->cookies->all())['user']);
        $this->addCsrfToken();
        return $this->render('userProfile.php', ['user' => $result, 'csrfToken' => self::$csrfToken]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveUserProfileAction () {
        $this->addCsrfToken();
        $user = $this->newUserProfile()->getAllFields();
        $avatar = $this->request->files->get('avatar');
        $validate = new Validate();

        $errors = $validate->userProfileFormValidate($this->app, $this->request, $user);

        if (!empty($errors)) {
            return $this->render('userProfile.php',
                ['user' => $this->newUserProfileModel()->getUserIdAvatarFioPhoneLoginByToken(($this->request->cookies->all())['user']),
                    'errors' => $errors, 'csrfToken' => self::$csrfToken]);
        }

        if (!empty($avatar)) {
            $errors = $validate->imageValidate($this->app, ['picture' => $avatar]);

            if (!empty($errors)) {
                return $this->render('userProfile.php',
                    ['user' => $this->newUserProfileModel()->getUserIdAvatarFioPhoneLoginByToken(($this->request->cookies->all())['user']),
                        'errors' => $errors, 'csrfToken' => self::$csrfToken]);
            }

            $avatarPath = 'avatars/' . ($this->app['uploader.helper']->upload($avatar, '/home/smelnikova/dev/my_shop.dev/web/avatars'));
        } else {
            $avatarPath = ($this->newUserProfileModel()->getUserIdAvatarFioPhoneLoginByToken(($this->request->cookies->all())['user']))['avatar'];
        }

        $userProfileModel = $this->newUserProfileModel();
        $userProfileModel->updateUserAvatarFioPhoneLogin($user, $avatarPath);
        return $this->render('userProfile.php', ['user' => $this->newUserProfileModel()->getUserIdAvatarFioPhoneLoginByToken(($this->request->cookies->all())['user']), 'csrfToken' => self::$csrfToken]);
    }

}