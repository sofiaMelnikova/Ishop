<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
use Application\ValueObject\GoodFields;
class Goods
{
    private $dataBase = null;
    private $stokeId;

    /**
     * Goods constructor.
     * @param DbQuery $dataBase
     */
    function __construct(DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param string $picture
     * @param GoodFields $goodFields
     * @return bool
     */
    public function addGood (string $picture, GoodFields $goodFields) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        try {
            $query = "SELECT `kinds`.`id` FROM `kinds` WHERE `kinds`.`kinds_value` = :kind;";
            $forExecute = [':kind' => $goodFields->getKind()];
            $kind = $this->dataBase->getData($query, $forExecute, false);
            $query = "INSERT INTO `stoke` (`kinds_id`, `count`, `cost`, `picture`, `product_name`) VALUES (:kindsId, :count, :cost, :picture, :product_name);";
            $forExecute = [':kindsId' => $kind['id'], ':count' => $goodFields->getCount(),
                ':cost' => $goodFields->getCost(), ':picture' => $picture, ':product_name' => $goodFields->getProductName()];
            $this->stokeId = $this->dataBase->changeData($query, $forExecute);

            $this->addProperties('color', $goodFields->getColor())->addProperties('size', $goodFields->getSize())->addProperties('material', $goodFields->getMaterial())->addProperties('gender', $goodFields->getGender());

            $connection->commit();
            return true;

        } catch (\PDOException $e) {
            $connection->rollBack();
            return false;
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    private function addProperties (string $name, $value) {
        if (!empty($value)) {
            $query = "INSERT INTO `properties` (`key`, `value`, `stoke_id`) VALUES (:key, :value, :stoke_id);";
            $forExecute = [':key' => $name, ':value' => $value, ':stoke_id' => $this->stokeId];
            $this->dataBase->changeData($query, $forExecute);
        }
        return $this;
    }

    /**
     * @param string $kind
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getNamePicturePriceOfKind (string $kind, int $startElement, int $countElements) {
        $query = "SELECT `stoke`.`id`, `stoke`.`product_name`, `stoke`.`picture`, `stoke`.`cost` FROM `stoke`, `kinds` 
                  WHERE `stoke`.`kinds_id`=`kinds`.`id` AND `stoke`.`is_delete` = 0 
                  AND `kinds`.`kinds_value` = :kind" . " LIMIT " . ($startElement-1) . ", " . $countElements;
        $forExecute = [':kind' => $kind];
        return $this->dataBase->getData($query, $forExecute);
    }

    /**
     * @param int $idStoke
     * @return array|mixed
     */
    public function getAllOfProduct (int $idStoke) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        try {
            $query = "SELECT `stoke`.`id`, `kinds`.`kinds_value`, `stoke`.`product_name`, `stoke`.`picture`, 
                        `stoke`.`cost`, `stoke`.`count` FROM `stoke`, `kinds` 
                        WHERE `stoke`.`id` = :stokeId AND `stoke`.`kinds_id` = `kinds`.`id` AND `stoke`.`is_delete` = 0";
            $forExecute = [':stokeId' => $idStoke];
            $result = $this->dataBase->getData($query, $forExecute, false);
            $properties = $this->getProperties($idStoke);
            if ($properties) {
                foreach ($properties as $property) {
                    $result = array_merge($result, [$property['key'] => $property['value']]);
                }
            }
            $connection->commit();
            return $result;

        } catch (\PDOException $e) {
            $connection->rollBack();
            return false;
        }
    }

    /**
     * @param int $stokeId
     * @return array|mixed
     */
    private function getProperties (int $stokeId) {
        $query = "SELECT `properties`.`key`, `properties`.`value` FROM `properties` WHERE `properties`.`stoke_id` = :stokeId;";
        $forExecute =[':stokeId' => $stokeId];
        return $this->dataBase->getData($query, $forExecute);
    }

    /**
     * @param string|null $kind
     * @return array|mixed
     */
    public function getCountProducts (string $kind = null) {
        if (!empty($kind)) {
            $query = "SELECT COUNT(*) FROM `stoke`, `kinds` WHERE `kinds`.`kinds_value` = :kind 
                      AND `stoke`.`kinds_id` = `kinds`.`id` AND `stoke`.`is_delete` = 0;";
            $forExecute = [':kind' => $kind];
            $result = $this->dataBase->getData($query, $forExecute, false);
        } else {
            $query = "SELECT COUNT(*) FROM `stoke` WHERE `stoke`.`is_delete` = 0;";
            $result = $this->dataBase->getData($query, [], false);
        }
        return $result;
    }

    /**
     * @param int $startElement
     * @param int $countElements
     * @return array|mixed
     */
    public function getPictureNameProduct (int $startElement, int $countElements) {
        $query = "SELECT `stoke`.`id`, `stoke`.`product_name`, `stoke`.`picture` FROM `stoke` WHERE `stoke`.`is_delete` = 0 LIMIT "  . ($startElement-1) . ", " . $countElements;
        return $this->dataBase->getData($query);
    }

    /**
     * @param int $stokeId
     */
    public function deleteProduct (int $stokeId) {
        $query = "UPDATE `stoke` SET `stoke`.`is_delete` = '1' WHERE `stoke`.`id` = :id;";
        $forExecute = [':id' => $stokeId];
        $this->dataBase->changeData($query, $forExecute);
    }

    /**
     * @param array $product
     * @return bool
     */
    public function updateProduct (array $product) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        try {
            $query = "UPDATE `stoke` SET `stoke`.`kinds_id` = :kind, `stoke`.`count` = :count, `stoke`.`cost` = :cost, 
                 `stoke`.`product_name` = :productName WHERE `stoke`.`id` = :id";
            $forExecute = [':kind' => $product['kind'], ':count' => $product['count'], ':cost' => $product['cost'],
                ':productName' => $product['productName'], ':id' => $product['id']];
            if (!empty($product['picture'])) {
                $query = "UPDATE `stoke` SET `stoke`.`kinds_id` = :kind, `stoke`.`count` = :count, `stoke`.`cost` = :cost, 
                  `stoke`.`picture` = :picture, `stoke`.`product_name` = :productName WHERE `stoke`.`id` = :id";
                $forExecute = [':kind' => $product['kind'], ':count' => $product['count'], ':cost' => $product['cost'],
                    ':picture' => $product['picture'], ':productName' => $product['productName'], ':id' => $product['id']];
            }
            $this->dataBase->changeData($query, $forExecute);
            $this->updateProductProperty('color', $product)->updateProductProperty('size', $product)
                ->updateProductProperty('material', $product)->updateProductProperty('gender', $product);
            $connection->commit();
            return true;
        } catch (\PDOException $e) {
            $connection->rollBack();
            return false;
        }
    }

    /**
     * @param string $nameProperty
     * @param array $product
     * @return $this
     */
    private function updateProductProperty (string $nameProperty, array $product) {
        if (!empty($product[$nameProperty])) {
            $query = "UPDATE `properties` SET `properties`.`value` = :newValue 
                      WHERE `properties`.`key` = :nameProperty AND `properties`.`stoke_id` = :stokeId;";
            $forExecute = [':newValue' => $product[$nameProperty], ':nameProperty' => $nameProperty, ':stokeId' => $product['id']];
            $this->dataBase->changeData($query, $forExecute);
        }
        return $this;
    }

    /**
     * @param int $userId
     * @param $basket
     */
    public function createNewOrder (int $userId, $basket) {
        $date = date("Y-m-d H:i");
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        foreach ($basket as $value) {
            $query = "INSERT INTO `orders` (`orders`.`users_id`, `orders`.`stoke_id`, `orders`.`count`, `orders`.`summ_cost`,
                  `orders`.`date`) VALUES (:userId, :stokeId, :count, :sumCost, :date);";
            $forExecute = [':userId' => $userId, ':stokeId' => $value['id'], ':count' => $value['countInBasket'],
                ':sumCost' => $value['sum'], ':date' => $date];
            $this->dataBase->changeData($query, $forExecute);
        }
        $connection->commit();
    }

}