<?php

namespace Bleidd\Database\Adapter;

use Bleidd\Application\Runtime;
use Bleidd\Database\AdapterInterface;
use Bleidd\Database\Exception\QueryException;
use Bleidd\Database\Exception\ConnectionException;

class PDO implements AdapterInterface
{

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $driver;

    /** @var string */
    private $database;

    /** @var \PDO */
    private $connection;

    /** @var string */
    private $sqlString;

    /**
     * PDO constructor
     *
     * @throws ConnectionException
     */
    public function __construct()
    {
        $dbConfig = Runtime::config()->configKey('db');

        $this->database = $dbConfig['database'] ?? null;
        $this->user = $dbConfig['user'] ?? null;
        $this->password = $dbConfig['password'] ?? null;
        $this->host = $dbConfig['host'] ?? null;
        $this->port = $dbConfig['port'] ?? 3306;
        $this->driver = $dbConfig['driver'] ?? 'mysql';
        
        try {
            $this->connection = new \PDO(
                sprintf('%s:host=%s;dbname=%s;port=%s', $this->driver, $this->host, $this->database, $this->port),
                $this->user, $this->password
            );
        } catch (\Exception $e) {
            throw new ConnectionException(sprintf('Could not create connection with database.'));
        }
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getSqlString(): string
    {
        return $this->sqlString;
    }

    /**
     * @throws QueryException
     * @param string $sqlString
     * @return array
     */
    public function query(string $sqlString): array
    {
        $this->sqlString = $sqlString;
        $stmt = $this->connection->query($sqlString);
        
        if (!$stmt) {
            throw new QueryException($this->connection->errorInfo()[2] ?? 'Could not perform SQL query');
        }

        return $this->getResult($stmt);
    }

    /**
     * @throws QueryException
     * @param string $sqlString
     * @param array  $params
     * @return int
     */
    public function execute(string $sqlString, array $params = []): int
    {
        $this->sqlString = $sqlString;
        $stmt = $this->connection->prepare($sqlString);

        if (!$stmt->execute($params)) {
            throw new QueryException($stmt->errorInfo()[2] ?? 'Could not perform SQL query');
        }

        return $stmt->rowCount();
    }

    /**
     * @param string $table
     * @return bool
     */
    public function truncate(string $table): bool
    {
        return (bool) $this->connection->exec('TRUNCATE TABLE ' . $table);
    }
    
    /**
     * @return int
     */
    public function lastInsertedId(): int
    {
        return $this->connection->lastInsertId();
    }

    /**
     * @param \PDOStatement $stmt
     * @return array
     */
    private function getResult(\PDOStatement $stmt): array
    {
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $result;
    }

}
