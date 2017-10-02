<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 18.08.17
 * Time: 14:05
 */

namespace Engine;

class DbQuery
{
    /**
     * @var \PDO
     */
    private $dbh;

    /**
     * DbQuery constructor.
     * @param string $dbName
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(string $dbName, string $host = '127.0.0.1', string $user, string $password) {
        $this->dbh = new \PDO('mysql:dbname=' . $dbName . ';host=' . $host, $user, $password);
        $this->dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return \PDO
     */
    public function getConnection () {
        return $this->dbh;
    }

    /**
     * @param string $query
     * @param array $params
     * @param bool $fetchAll
     * @return array
     */
    public function select (string $query, array $params = [], bool $fetchAll = true):array {
        $sth = $this->dbh->prepare($query);

        $sth = $this->execute($sth, $params);

        $sth->execute();

        if ($fetchAll) {
            return $sth->fetchAll();
        }

        return ($result = $sth->fetch()) ? $result : [];
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     */
    public function update (string $query, array $params = []) {
        $sth = $this->dbh->prepare($query);

        $sth = $this->execute($sth, $params);

        $sth->execute();
        return $sth->rowCount();
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     */
    public function delete (string $query, array $params = []) {
        $sth = $this->dbh->prepare($query);

        $sth = $this->execute($sth, $params);

        $sth->execute();
        return $sth->rowCount();
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     */
    public function insert (string $query, array $params = []):int {
        $sth = $this->dbh->prepare($query);

        $result = $this->execute($sth, $params)->execute();

        if (!$result) {
            return 0;
        }

        return intval($this->dbh->lastInsertId());
    }

    /**
     * @param \PDOStatement $sth
     * @param array $params
     * @return \PDOStatement
     */
    private function execute (\PDOStatement $sth, array $params) {
        if (empty($params)) {
            return $sth;
        }

        foreach ($params as $key => $value) {
            if (!empty($value['value'])) {
                $value['type'] = (empty($value['type'])) ? (\PDO::PARAM_STR) : $value['type'];
                $sth->bindValue($key, $value['value'], $value['type']);
            }
        }

        return $sth;
    }
}