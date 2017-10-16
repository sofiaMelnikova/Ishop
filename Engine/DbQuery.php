<?php
namespace Engine;

use Silex\Application;

class DbQuery
{
    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var Application
     */
    private $app;


    /**
     * DbQuery constructor.
     * @param string $dbName
     * @param string $host
     * @param string $user
     * @param string $password
     * @param Application $app
     */
    public function __construct(string $dbName, string $host = '127.0.0.1', string $user, string $password, Application $app) {
        $this->connection = new \PDO('mysql:dbname=' . $dbName . ';host=' . $host, $user, $password);
        $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->app = $app;
    }

    /**
     * @return \PDO
     */
    public function getConnection () {
        return $this->connection;
    }

    /**
     * @param string $query
     * @param array $params
     * @param bool $fetchAll
     * @return array
     */
    public function select (string $query, array $params = [], bool $fetchAll = true):array {
        $sth = $this->connection->prepare($query);

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
        $sth = $this->connection->prepare($query);

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
        $sth = $this->connection->prepare($query);

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
        $sth = $this->connection->prepare($query);

        $result = $this->execute($sth, $params)->execute();

        return (!$result) ? 0 : intval($this->connection->lastInsertId());

    }

    /**
     * @param \PDOStatement $sth
     * @param array $params
     * @return \PDOStatement
     * @throws \Exception
     */
    private function execute (\PDOStatement $sth, array $params) {
        if (empty($params)) {
            return $sth;
        }

        foreach ($params as $key => $value) {
            if (empty($value['type'])) {
                $value['type'] = \PDO::PARAM_STR;
            } elseif (!$this->isTypeValid($value['type'])) {
                throw new \Exception("Type variable must be one of there are: \PDO::PARAM_NULL, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_LOB, \PDO::PARAM_STMT or empty.");
            }

            $sth->bindValue($key, $value['value'], $value['type']);
        }

        return $sth;
    }

    private function isTypeValid(string $type):bool {
        $types = [\PDO::PARAM_NULL, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_LOB, \PDO::PARAM_STMT];
        return in_array($type, $types);
    }
}