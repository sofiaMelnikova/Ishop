<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 20.08.17
 * Time: 9:51
 */

namespace Application\Validate;

use Application\Models\RegistrationModel;
use Application\Models\UserProfileModel;
use \Symfony\Component\HttpFoundation\Request;
use Silex\Application;

use Symfony\Component\Validator\Constraints as Assert;

class Validate
{

    /**
     * @param Application $app
     * @param Request $request
     * @param array $values
     * @return array
     */
    public function userProfileFormValidate (Application $app, Request $request, array $values) {
        $registrationModel = new RegistrationModel($app);
        $userId = $registrationModel->getUserByPhone($values['phone']);
        $isUserExist = $registrationModel->isLoginExist($values['login']);
        $errors = [];

        $user = $app['userProfile.model']->getUserIdAvatarFioPhoneLoginByToken(($request->cookies->all())['user']);

        if (!empty($userId) && ($values['phone'] != $user['phone'])) {
            array_push($errors, "This phone already exist");
        }

        if ($isUserExist && ($values['login'] != $user['login'])) {
            array_push($errors, 'User already exist with this login.');
        }

        $constraint = new Assert\Collection(['id' => new Assert\Type(['type' => 'numeric']),
            'login' => $this->forLogin(),
            'phone' => $this->forPhone(),
            'fio' => $this->forFio()]);
        $err = $app['validator']->validate($values, $constraint);

        if (count($err) > 0) {
            foreach ($err as $error) {
                array_push($errors, $error->getPropertyPath() . ' ' . $error->getMessage());
            }
        }

        return $errors;
    }

    /**
     * @param Application $app
     * @param array $values
     * @return array
     */
    public function registrationFormValidate (Application $app, array $values) {
        $registrationModel = new RegistrationModel($app);
        $isUserExist = $registrationModel->isLoginExist($values['email']);
        $err = [];

        if ($isUserExist) {
            array_push($err, 'User already exist with this login.');
        }

        $userId = $registrationModel->getRegistretedUserByPhone($values['phone']);

        if ($userId) {
            array_push($err, 'Phone already exist.');
        }

        $constraint = new Assert\Collection(['email' => new Assert\Email(),
            'phone' => $this->forPhone()]);


        $errors = $app['validator']->validate($values, $constraint);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                array_push($err, $error->getPropertyPath() . ' ' . $error->getMessage());
            }
        }

        return $err;
    }

    /**
     * @return array
     */
    private function forFio () {
        return [new Assert\Type(['type' => 'string'])];
    }

    /**
     * @return array
     */
    private function forLogin () {
        return [new Assert\Type(['type' => 'string']), new Assert\NotNull(['message' => 'Error: producer is empty.'])];
    }

    /**
     * @param Application $app
     * @param array $phone
     * @return array
     */
    public function phoneValidate (Application $app, array $phone) {
        $constraint = new Assert\Collection(['phone' => $this->forPhone()]);
        $errors = $app['validator']->validate($phone, $constraint);
        $err = [];

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                array_push($err, $error->getPropertyPath() . ' ' . $error->getMessage());
            }
        }

        return $err;
    }

    /**
     * @return array
     */
    private function forPhone () {
        return [new Assert\NotNull(['message' => 'Please, add your phone number.']),
            new Assert\Type(['type' => 'numeric', 'message' => 'Error: Phone must be number']),
            new Assert\Length(['min' => 11, 'max' => 11, 'maxMessage' => 'Phone must have 11 character.', 'minMessage' => 'Phone must have 11 character.'])];
    }

    /**
     * @param Application $app
     * @param array $values
     * @return string
     */
    public function shoesFormValidate (Application $app, array $values):string {
        $constraint = new Assert\Collection(['stokeId' => $this->forStokeId(),
            'kind' => $this->forKind(),
            'productName' => $this->forProductName(),
            'brand' => $this->forBrand(),
            'color' => $this->forColor(),
            'size' => $this->forSizeShoes(),
            'material' => $this->forMaterial(),
            'gender' => $this->forGender($values['gender']),
            'producer' => $this->forProducer(),
            'count' => $this->forCount(),
            'cost' => $this->forCost()]);
        return $this->getValidateString($app, $values, $constraint);
    }

    /**
     * @param Application $app
     * @param array $values
     * @return string
     */
    public function jacketFormValidate (Application $app, array $values):string {
        $constraint = new Assert\Collection(['stokeId' => $this->forStokeId(),
            'kind' => $this->forKind(),
            'productName' => $this->forProductName(),
            'brand' => $this->forBrand(),
            'color' => $this->forColor(),
            'size' => $this->forSizeJacket(),
            'material' => $this->forMaterial(),
            'gender' => $this->forGender($values['gender']),
            'producer' => $this->forProducer(),
            'count' => $this->forCount(),
            'cost' => $this->forCost()]);
        return $this->getValidateString($app, $values, $constraint);
    }

    /**
     * @param Application $app
     * @param array $values
     * @return string
     */
    public function plaidFormValidate (Application $app, array $values):string {
        $constraint = new Assert\Collection(['stokeId' => $this->forStokeId(),
            'kind' => $this->forKind(),
            'productName' => $this->forProductName(),
            'color' => $this->forColor(),
            'length' => $this->forLengthAndWidth(),
            'width' => $this->forLengthAndWidth(),
            'material' => $this->forMaterial(),
            'producer' => $this->forProducer(),
            'count' => $this->forCount(),
            'cost' => $this->forCost()]);
        return $this->getValidateString($app, $values, $constraint);
    }

    /**
     * @param Application $app
     * @param array $values
     * @param Assert\Collection $constraint
     * @return string
     */
    private function getValidateString (Application $app, array $values, Assert\Collection $constraint):string {
        $err = '';
        $errors = $app['validator']->validate($values, $constraint);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $err = $err . $error->getPropertyPath() . ' ' . $error->getMessage() . "\n";
            }
        }
        return $err;
    }

    /**
     * @return Assert\Type
     */
    private function forStokeId () {
        return new Assert\Type(['type' => 'numeric', 'message' => 'Error: length must be number']);
    }

    /**
     * @return Assert\NotNull
     */
    private function forKind () {
        return new Assert\NotNull(['message' => 'Error: kind is empty.']);
    }

    /**
     * @return array
     */
    private function forProductName () {
        return [new Assert\NotNull(['message' => 'Error: Product`s Name incorrect.']),
            new Assert\Type(['type' => 'string', 'message' => 'Error: Product`s Name must be string'])];
    }

    /**
     * @return array
     */
    private function forSizeShoes () {
        return [new Assert\Range(['min' => 36, 'max' => 46, 'invalidMessage' => 'Shoes size mast have range in between from 36 to 46']),
            new Assert\NotNull(['message' => 'Error: brand is empty.'])];
    }

    /**
     * @return array
     */
    private function forSizeJacket () {
        return [new Assert\Range(['min' => 38, 'max' => 56, 'invalidMessage' => 'Jacket size mast have range in between from 38 to 56']),
            new Assert\NotNull(['message' => 'Error: brand is empty.'])];
    }

    /**
     * @return array
     */
    private function forBrand () {
        return [new Assert\Type(['type' => 'string', 'message' => 'Error: brand must be string']),
            new Assert\NotNull(['message' => 'Error: brand is empty.'])];

    }

    /**
     * @param $gender
     * @return Assert\EqualTo|Assert\NotNull
     */
    private function forGender ($gender) {
        if (is_null($gender)) {
            return new Assert\NotNull(['message' => 'Error: gender is empty.']);
        }
        if ($gender === 'man') {
            return new Assert\EqualTo(['value' => 'man', 'message' => 'Error: gender must be man or woman']);
        }
        return new Assert\EqualTo(['value' => 'woman', 'message' => 'Error: gender must be man or woman']);
    }

    /**
     * @return array
     */
    private function forColor () {
        return [new Assert\NotNull(['message' => 'Error: color is empty.']), new Assert\Type(['type' => 'string'])];
    }

    /**
     * @return array
     */
    private function forMaterial () {
        return [new Assert\NotNull(['message' => 'Error: material is empty.']), new Assert\Type(['type' => 'string'])];
    }

    /**
     * @return array
     */
    private function forProducer () {
        return [new Assert\Type(['type' => 'string']), new Assert\NotNull(['message' => 'Error: producer is empty.'])];
    }

    /**
     * @return array
     */
    private function forLengthAndWidth () {
        return [new Assert\Type(['type' => 'numeric', 'message' => 'Error: length must be number']), new Assert\NotNull(['message' => 'Error: length is empty.'])];

    }

    /**
     * @return array
     */
    private function forCost () {
        return [new Assert\NotNull(['message' => 'Error: Cost incorrect.']),
            new Assert\Type(['type' => 'numeric', 'message' => 'Error: Cost must be number'])];
    }

    /**
     * @return array
     */
    private function forCount () {
        return [new Assert\NotNull(['message' => 'Error: count incorrect.']),
            new Assert\Type(['type' => 'numeric', 'message' => 'Error: Count must be number'])];
    }

    /**
     * @param Application $app
     * @param $picture
     * @return string
     */
    public function imageValidate (Application $app, $picture) {
        $constraint = new Assert\Collection(['picture' => $this->forImage()]);
        $errors = $app['validator']->validate($picture, $constraint);
        $err = '';
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $err = $err . $error->getPropertyPath() . ' ' . $error->getMessage() . "\n";
            }
        }
        return $err;
    }

    /**
     * @return array
     */
    private function forImage () {
        return [new Assert\File(['maxSize' => '2M']), new Assert\Image(['mimeTypes' => ['image/gif', 'image/jpeg', 'image/png', 'image/jpg'],
            'mimeTypesMessage' => 'Your picture is not gif, jpeg, jpg or png'])];
    }

}