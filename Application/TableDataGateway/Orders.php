<?php
namespace Application\TableDataGateway;

use Engine\DbQuery;
use PDO;

class Orders
{
    /**
     * @var DbQuery|null
     */
    private $dataBase = null;

    /**
     * Goods constructor.
     * @param DbQuery $dataBase
     */
    function __construct(DbQuery $dataBase) {
        $this->dataBase = $dataBase;
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
     * @return int
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