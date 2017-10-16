<?php
namespace Application\TableDataGateway;

use Engine\DbQuery;
use PDO;

class Filters
{
    /**
     * @var DbQuery|null
     */
    private $dataBase = null;

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var int
     */
    private $endSelect = 0;

    /**
     * @var int
     */
    private $inc = 0;

    /**
     * Goods constructor.
     * @param DbQuery $dataBase
     */
    function __construct(DbQuery $dataBase) {
        $this->dataBase = $dataBase;
    }

    /**
     * @param string $kind
     * @param array $filters
     * @return array
     */
    // ['namePropertyInBD' => ['value' => ['min' => , 'max' => ]/'value', 'type' => ]
    public function getCountProductsByFilters (string $kind, array $filters) {
        $this->query = "SELECT COUNT(*) FROM `stoke` WHERE `stoke`.`is_delete` = 0 ";

        if (!empty($filters['cost']['value']['min']) || !empty($filters['cost']['value']['max'])) {
            $this->query = $this->query . "AND `stoke`.`id` IN (SELECT `stoke`.`id` FROM `stoke`, `kinds` WHERE `kinds`.`kinds_value` = :kind ";
            $this->params = array_merge($this->params, [':kind' => ['value' => $kind, 'type' => PDO::PARAM_STR]]);

            if (!empty($filters['cost']['value']['min'])) {
                $this->query = $this->query . "AND `stoke`.`kinds_id` = `kinds`.`id` AND (`stoke`.`cost` > :min OR `stoke`.`cost` = :min) ";
                $this->params = array_merge($this->params, [':min' => ['value' => $filters['cost']['value']['min'], 'type' => $filters['cost']['type']]]);
            }

            if (!empty($filters['cost']['value']['max'])) {
                $this->query = $this->query . "AND `stoke`.`kinds_id` = `kinds`.`id` AND (`stoke`.`cost` < :max OR `stoke`.`cost` = :max) ";
                $this->params = array_merge($this->params, [':max' => ['value' => $filters['cost']['value']['max'], 'type' => $filters['cost']['type']]]);
            }

            $this->endSelect++;
        }

        unset($filters['cost']);

        foreach ($filters as $key => $value) {
            $this->addFilterToRequest($key, $value, $kind);
        }

        for ($i = 0; $i < $this->endSelect; $i++) {
            $this->query = $this->query . ')';
        }

        return $this->dataBase->select($this->query, $this->params, false);
    }

    /**
     * @param string $kind
     * @param array $filters
     * @param int $startElement
     * @param int $countElements
     * @return array
     */
    // ['namePropertyInBD' => ['value' => ['min' => , 'max' => ]/'value', 'type' => ]
    public function getProductsByFiltersInLimit (string $kind, array $filters, int $startElement, int $countElements) {

        $this->query = "SELECT `stoke`.`id`, `stoke`.`product_name`, `stoke`.`picture`, `stoke`.`cost` FROM `stoke`, `kinds` 
          WHERE `stoke`.`kinds_id`=`kinds`.`id` AND `stoke`.`is_delete` = 0 AND `kinds`.`kinds_value` = :kind ";

        if (!empty($filters['cost']['value']['min']) || !empty($filters['cost']['value']['max'])) {
            $this->query = $this->query . "AND `stoke`.`id` IN (SELECT `stoke`.`id` FROM `stoke`, `kinds`, `properties` WHERE `properties`.`stoke_id` = `stoke`.`id` AND `kinds`.`kinds_value` = :kind ";
            $this->params = array_merge($this->params, [':kind' => ['value' => $kind, 'type' => PDO::PARAM_STR]]);

            if (!empty($filters['cost']['value']['min'])) {
                $this->query = $this->query . "AND `stoke`.`kinds_id` = `kinds`.`id` AND (`stoke`.`cost` > :min OR `stoke`.`cost` = :min) ";
                $this->params = array_merge($this->params, [':min' => ['value' => $filters['cost']['value']['min'], 'type' => $filters['cost']['type']]]);
            }

            if (!empty($filters['cost']['value']['max'])) {
                $this->query = $this->query . "AND `stoke`.`kinds_id` = `kinds`.`id` AND (`stoke`.`cost` < :max OR `stoke`.`cost` = :max) ";
                $this->params = array_merge($this->params, [':max' => ['value' => $filters['cost']['value']['max'], 'type' => $filters['cost']['type']]]);
            }

            $this->endSelect++;
        }

        unset($filters['cost']);

        foreach ($filters as $key => $value) {
            $this->addFilterToRequest($key, $value, $kind);
        }

        for ($i = 0; $i < $this->endSelect; $i++) {
            $this->query = $this->query . ')';
        }

        $this->query = $this->query . "LIMIT :startElement, :countElements";
        $this->params = array_merge($this->params, [':startElement' => ['value' => $startElement - 1, 'type' => PDO::PARAM_INT],
            ':countElements' => ['value' => $countElements, 'type' => PDO::PARAM_INT]]);

        return $this->dataBase->select($this->query, $this->params);
    }

    /**
     * @param string $nameProperty
     * @param array $value
     * @param string $kind
     * @return $this
     */
    private function addFilterToRequest (string $nameProperty, array $value, string $kind) {
        if ((is_array($value['value']) && empty($value['value']['max']) && empty($value['value']['min'])) || (empty($value['value']))) {
            return $this;
        }

        $this->query = $this->query . "AND `stoke`.`id` IN
        (SELECT `stoke`.`id` FROM `stoke`, `kinds`, `properties`
          WHERE `properties`.`stoke_id` = `stoke`.`id` AND `stoke`.`kinds_id`=`kinds`.`id` AND `stoke`.`is_delete` = 0 AND `kinds`.`kinds_value` = :kind
        AND `properties`.`stoke_id` = `stoke`.`id` ";
        $this->params = array_merge($this->params, [':kind' => ['value' => $kind, 'type' => PDO::PARAM_STR]]);


        if (is_array($value['value']) && !empty($value['value']['min']) ) {
            $this->query = $this->query . "AND (`properties`.`key` = :nameProperty" . $this->inc . " AND (`properties`.`value` > :value" . $this->inc . " OR `properties`.`value` = :value" . $this->inc . ")) ";
            $this->params = array_merge($this->params, [':nameProperty' . $this->inc => ['value' => $nameProperty, 'type' => PDO::PARAM_STR],
                ':value' . $this->inc => ['value' => $value['value']['min'], 'type' => $value['type']]]);
        } elseif (is_array($value['value']) && !empty($value['value']['max'])) {
            $this->query = $this->query . "AND (`properties`.`key` = :nameProperty" . $this->inc . " AND (`properties`.`value` < :value" . $this->inc . " OR `properties`.`value` = :value" . $this->inc . ")) ";
            $this->params = array_merge($this->params, [':nameProperty' . $this->inc => ['value' => $nameProperty, 'type' => PDO::PARAM_STR],
                ':value' . $this->inc => ['value' => $value['value']['max'], 'type' => $value['type']]]);
        } else {
            $this->query = $this->query . "AND (`properties`.`key` = :nameProperty" . $this->inc . " AND `properties`.`value` = :value" . $this->inc . ") ";
            $this->params = array_merge($this->params, [':nameProperty' . $this->inc => ['value' => $nameProperty, 'type' => PDO::PARAM_STR],
                ':value' . $this->inc => ['value' => $value['value'], 'type' => $value['type']]]);
        }

        $this->endSelect++;
        $this->inc++;

        return $this;
    }
}