<?php

namespace Application\TableDataGateway;

use Engine\DbQuery;
use PDO;

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
     * @param array $property
     * @param array $propertyKeys
     * @return bool
     */
    public function addGood (string $picture, array $property, array $propertyKeys) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        try {
            $query = "SELECT `kinds`.`id` FROM `kinds` WHERE `kinds`.`kinds_value` = :kind;";
            $params = [':kind' => ['value' => $property['kind'], 'type' => PDO::PARAM_STR]];
            $kind = $this->dataBase->select($query, $params,false);
            $query = "INSERT INTO `stoke` (`kinds_id`, `count`, `cost`, `picture`, `product_name`)
                      VALUES (:kindsId, :count, :cost, :picture, :product_name);";
            $params = [':kindsId' => ['value' => $kind['id'], 'type' => PDO::PARAM_INT],
                ':count' => ['value' => $property['count'], 'type' => PDO::PARAM_INT],
                ':cost' => ['value' => $property['cost'], 'type' => PDO::PARAM_INT],
                ':picture' => ['value' => $picture, 'type' => PDO::PARAM_STR],
                ':product_name' => ['value' => $property['productName'], 'type' => PDO::PARAM_STR]];
            $this->stokeId = $this->dataBase->insert($query, $params);

            foreach ($propertyKeys as $propertyName) {
               $this->addProperties($propertyName, $property[$propertyName]);
            }

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
            $params = [':key' => ['value' => $name, 'type' => PDO::PARAM_STR],
                ':value' => ['value' => $value, 'type' => PDO::PARAM_STR],
                ':stoke_id' => ['value' => $this->stokeId, 'type' => PDO::PARAM_INT]];
            $this->dataBase->insert($query, $params);
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
        $params = [':kind' => ['value' => $kind, 'type' => PDO::PARAM_STR]];
        return $this->dataBase->select($query, $params);
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
            $params = [':stokeId' => ['value' => $idStoke, 'type' => PDO::PARAM_INT]];
            $result = $this->dataBase->select($query, $params,false);
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
        $params = [':stokeId' => ['value' => $stokeId, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params);
    }

    /**
     * @param string|null $kind
     * @return array|mixed
     */
    public function getCountProducts (string $kind = null) {
        if (!empty($kind)) {
            $query = "SELECT COUNT(*) FROM `stoke`, `kinds` WHERE `kinds`.`kinds_value` = :kind 
                      AND `stoke`.`kinds_id` = `kinds`.`id` AND `stoke`.`is_delete` = 0;";
            $params = [':kind' => ['value' => $kind, 'type' => PDO::PARAM_STR]];
            $result = $this->dataBase->select($query, $params,false);
        } else {
            $query = "SELECT COUNT(*) FROM `stoke` WHERE `stoke`.`is_delete` = 0;";
            $result = $this->dataBase->select($query, [],false);
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
        return $this->dataBase->select($query);
    }

    /**
     * @param int $stokeId
     */
    public function deleteProduct (int $stokeId) {
        $query = "UPDATE `stoke` SET `stoke`.`is_delete` = '1' WHERE `stoke`.`id` = :id;";
        $params = [':id' => ['value' => $stokeId, 'type' => PDO::PARAM_INT]];
        $this->dataBase->update($query, $params);
    }

    /**
     * @param array $product
     * @param array $propertyKeys
     * @return bool
     */
    public function updateProduct (array $product, array $propertyKeys) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        try {
            $query = "UPDATE `stoke` SET `stoke`.`count` = :countProduct, `stoke`.`cost` = :cost,
                 `stoke`.`product_name` = :productName WHERE `stoke`.`id` = :id;";
            $params = [':countProduct' => ['value' => $product['count'], 'type' => PDO::PARAM_INT],
                ':cost' => ['value' => $product['cost'], 'type' => PDO::PARAM_INT],
                ':productName' => ['value' => $product['productName'], 'type' => PDO::PARAM_STR],
                ':id' => ['value' => $product['stokeId'], 'type' => PDO::PARAM_INT]];
            if (!empty($product['picture'])) {
                $query = "UPDATE `stoke` SET `stoke`.`count` = :countProduct, `stoke`.`cost` = :cost, 
                  `stoke`.`picture` = :picture, `stoke`.`product_name` = :productName WHERE `stoke`.`id` = :id;";
                $params = [':countProduct' => ['value' => $product['count'], 'type' => PDO::PARAM_INT],
                    ':cost' => ['value' => $product['cost'], 'type' => PDO::PARAM_INT],
                    ':picture' => ['value' => $product['picture'], 'type' => PDO::PARAM_STR],
                    ':productName' => ['value' => $product['productName'], 'type' => PDO::PARAM_STR],
                    ':id' => ['value' => $product['stokeId'], 'type' => PDO::PARAM_INT]];
            }
            $this->dataBase->update($query, $params);
            foreach ($propertyKeys as $propertyKey) {
                $this->updateProductProperty($propertyKey, $product);
            }
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
            $params = [':newValue' => ['value' => $product[$nameProperty], 'type' => PDO::PARAM_STR],
                ':nameProperty' => ['value' => $nameProperty, 'type' => PDO::PARAM_STR],
                ':stokeId' => ['value' => $product['stokeId'], 'type' => PDO::PARAM_INT]];
            $this->dataBase->update($query, $params);
        }
        return $this;
    }

    /**
     * @param int $userId
     * @param array $basket
     */
    public function createAndExecuteNewOrder (int $userId, array $basket) {
        $date = date("Y-m-d H:i");
        $query = "INSERT INTO `orders` (`orders`.`users_id`, `orders`.`create_at`, `orders`.`executed_at`, `orders`.`is_basket`)
                  VALUES (:usersId, :createAt, :executedAt, :isBasket);";
        $params = [':usersId' => ['value' => $userId, 'type' => PDO::PARAM_INT],
            ':createAt' => ['value' => $date, 'type' => PDO::PARAM_STR],
            ':executedAt' => ['value' => $date, 'type' => PDO::PARAM_STR],
            ':isBasket' => ['value' => 0, 'type' => PDO::PARAM_INT]];
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        $ordersId = $this->dataBase->insert($query, $params);
        foreach ($basket as $value) {
            $query = "INSERT INTO `orders_item` (`orders_item`.`orders_id`, `orders_item`.`stoke_id`,
                      `orders_item`.`actual_cost`) VALUES (:ordersId, :stokeId, :actualCost);";
            $params = [':ordersId' => ['value' => $ordersId, 'type' => PDO::PARAM_INT],
                ':stokeId' => ['value' => $value['id'], 'type' => PDO::PARAM_INT],
                ':actualCost' => ['value' => $value['cost'], 'type' => PDO::PARAM_INT]];
            $this->dataBase->insert($query, $params);
        }
        $connection->commit();
    }

    /**
     * @param int $userId
     * @param array $product
     * @return bool|string
     */
    public function createNewOrder (int $userId, array $product) {
        $date = date("Y-m-d H:i:s");
        $query = "INSERT INTO `orders` (`orders`.`users_id`, `orders`.`create_at`) VALUES (:usersId, :createAt);";
        $params = [':usersId' => ['value' => $userId, 'type' => PDO::PARAM_INT],
            ':createAt' => ['value' => $date, 'type' => PDO::PARAM_STR]];
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        $ordersId = $this->dataBase->insert($query, $params);
        $query = "INSERT INTO `orders_item` (`orders_item`.`orders_id`, `orders_item`.`stoke_id`,
        `orders_item`.`actual_cost`) VALUES (:ordersId, :stokeId, :actualCost);";
        $params = [':ordersId' => ['value' => $ordersId, 'type' => PDO::PARAM_INT],
            ':stokeId' => ['value' => $product['id'], 'type' => PDO::PARAM_INT],
            ':actualCost' => ['value' => $product['cost'], 'type' => PDO::PARAM_INT]];
        $this->dataBase->insert($query, $params);
        $connection->commit();
        return $ordersId;
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getNumberOrdesInBasketForUser (int $userId) {
        $query = "SELECT `orders`.`id` FROM `orders` WHERE `orders`.`users_id` = :userId AND `orders`.`is_basket` = 1;";
        $params = [':userId' => ['value' => $userId, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params,false);
    }

    /**
     * @param int $numberOrder
     * @param array $product
     */
    public function addToOrderBasketProduct (int $numberOrder, array  $product) {
        $query = "INSERT INTO `orders_item` (`orders_item`.`orders_id`, `orders_item`.`stoke_id`, `orders_item`.`actual_cost`)
                  VALUES (:ordersId, :stokeId, :actualCost);";
        $params = [':ordersId' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT],
            ':stokeId' => ['value' => $product['id'], 'type' => PDO::PARAM_INT],
            ':actualCost' => ['value' => $product['cost'], 'type' => PDO::PARAM_INT]];
        $this->dataBase->insert($query, $params);
    }

    /**
     * @param int $numberOrder
     */
    public function formBusketToExecuted (int $numberOrder) {
        $date = date("Y-m-d H:i:s");
        $query = "UPDATE `orders` SET `orders`.`executed_at` = :executeDate, `orders`.`is_basket` = 0
                  WHERE `orders`.`id` = :numberOrder;";
        $params = [':executeDate' => ['value' => $date, 'type' => PDO::PARAM_STR],
            ':numberOrder' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT]];
        $this->dataBase->update($query, $params);
    }

    /**
     * @param int $numberOrder
     * @return array|mixed
     */
    public function getProductsFromBasket (int $numberOrder) {
        $query = "SELECT `orders_item`.`stoke_id`, `orders_item`.`actual_cost`
                  FROM `orders_item` WHERE `orders_item`.`orders_id` = :ordersId";
        $params = [':ordersId' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params);
    }

    /**
     * @param int $numberOrder
     * @return array|mixed
     */
    public function getCountProductsInBasket (int $numberOrder) {
        $query = "SELECT COUNT(*) FROM `orders_item` WHERE `orders_item`.`orders_id` = :ordersId";
        $params = [':ordersId' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params,false);
    }

    /**
     * @param int $numberOrder
     * @param int|null $stokeId
     */
    public function deleteFromBasket (int $numberOrder, int $stokeId = null) {
        $connection = $this->dataBase->getConnection();
        $connection->beginTransaction();
        if (!empty($stokeId)) {
            $query = "DELETE FROM `orders_item` WHERE `orders_item`.`orders_id` = :ordersId AND `orders_item`.`stoke_id` = :stokeId 
            ORDER BY `orders_item`.`id` DESC LIMIT 1";
            $params = [':ordersId' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT],
                ':stokeId' => ['value' => $stokeId, 'type' => PDO::PARAM_INT]];
            $this->dataBase->delete($query, $params);
        } else {
            $query = "DELETE FROM `orders_item` WHERE `orders_item`.`orders_id` = :ordersId";
            $params = [':ordersId' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT]];
            $this->dataBase->delete($query, $params);
            $query = "DELETE FROM `orders` WHERE `orders`.`id` = :id";
            $params = [':id' => ['value' => $numberOrder, 'type' => PDO::PARAM_INT]];
            $this->dataBase->delete($query, $params);
        }
        $connection->commit();
    }

    /**
     * @param int $userId
     * @return array|mixed
     */
    public function getInfoForHistoryOrdersByUserId (int $userId) {
        $query = "SELECT `orders_item`.`orders_id`, `stoke`.`product_name`, `orders_item`.`actual_cost`, `orders`.`executed_at`, 
                  `stoke`.`picture` FROM `orders_item`, `stoke`, `orders` WHERE `orders_item`.`orders_id` = `orders`.`id`
                  AND `orders`.`users_id` = :userId AND `stoke`.`id` = `orders_item`.`stoke_id`";
        $params = [':userId' => ['value' => $userId, 'type' => PDO::PARAM_INT]];
        return $this->dataBase->select($query, $params);
    }

}