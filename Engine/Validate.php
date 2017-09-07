<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 20.08.17
 * Time: 9:51
 */

namespace Engine;

use Silex\Application;
use Symfony\Component\Validator\Constraints as Assert;

class Validate
{
    /**
     * @param Application $app
     * @param $email
     * @return bool
     */
    public function isEmailValid (Application $app, $email) {
        $errors = $app['validator']->validate($email, new Assert\Email());
        if (count($errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param Application $app
     * @param array $values
     * @return string
     */
    public function formValidate (Application $app, array $values) {
        $err = '';
        $constraint = new Assert\Collection(array(
            'id' => new Assert\Regex(['pattern' => '/^[0-9]{0,11}$/', 'message' => 'Error: id incorrect.']),
            'kind' => new Assert\Regex(['pattern' => '/^[123]$/', 'message' => 'Error: kind incorrect.']),
            'productName' => [new Assert\Regex(['pattern' => '/^[a-zA-Z0-9 \-]{3,30}$/', 'message' => 'Error: product`s name incorrect.']), new Assert\NotNull(['message' => 'Error: product`s name must be not null.'])],
            'brand' => new Assert\Regex(['pattern' => '^[a-zA-Z0-9 \-]{3,30}$', 'message' => 'Error: brand incorrect.']),
            'color' => new Assert\Regex(['pattern' => '/^[a-zA-Z \-\,]{0,30}$/', 'message' => 'Error: color incorrect.']),
            'size' => $this->forSize($values['kind']) ,
            'material' => new Assert\Regex(['pattern' => '/^[a-zA-Z\-]{0,30}$/', 'message' => 'Error: material incorrect.']),
            'gender' => new Assert\Regex(['pattern' => '/^(man|woman|)$/', 'message' => 'Error: gender incorrect.']),
            'length' => new Assert\Regex(['pattern' => '/^[1-9][0-9\.]{0,5}$/', 'message' => 'Error: length incorrect.']),
            'width' => new Assert\Regex(['pattern' => '/^[1-9][0-9\.]{0,5}$/', 'message' => 'Error: width incorrect.']),
            'producer' => new Assert\Regex(['pattern' => '/^[a-zA-Z \-\,]{0,30}$/', 'message' => 'Error: producer incorrect.']),
            'count' => new Assert\NotNull(['message' => 'Error: count incorrect.']),
            'cost' => new Assert\NotNull(['message' => 'Error: cost incorrect.'])
        ));
        $errors = $app['validator']->validate($values, $constraint);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $err = $err . $error->getPropertyPath() . ' ' . $error->getMessage() . "\n";
            }
        }
        return $err;
    }

    /**
     * @param string $kind
     * @return Assert\Range
     */
    private function forSize (string $kind) {
        if ($kind === '1') {
            return new Assert\Range(['min' => 36, 'max' => 46, 'invalidMessage' => 'Shoes size mast have range in between from 36 to 46']);
        }
        if ($kind === '2') {
            return new Assert\Range(['min' => 38, 'max' => 56, 'invalidMessage' => 'Jacket size mast have range in between from 38 to 56']);
        }
//        if ($kind === '3') {
//            return new Assert\IsNull(['message' => 'kind must be null']);
//        }
    }


    private function forBrand () {

    }

    private function forGender () {

    }

    private function forColor () {

    }

    private function forMaterial () {

    }

    private function forProducer () {

    }

    private function forLength () {

    }

    private function forWidth () {

    }


}