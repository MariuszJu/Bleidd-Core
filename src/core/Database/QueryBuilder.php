<?php

namespace Bleidd\Database;

final class QueryBuilder
{

    /** @var string */
    private $sql;

    /** @var string */
    private $table;

    /**
     * QueryBuilder constructor
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string|\Closure $column
     * @param string|null     $operatorOrValue
     * @param null            $value
     * @param string          $concat
     */
    private function addWhere(string $column, string $operatorOrValue = null, $value = null, string $concat = 'AND')
    {
        if (empty($value)) {
            $operator = '=';
            $value = $operatorOrValue;
        } else {
            $operator = $operatorOrValue;
        }

        $this->bindParam($value);
        $sql = sprintf('%s %s %s', $column, $operator, $value);

        if (strpos($this->sql, 'WHERE') !== false) {
            $sql = sprintf('%s %s', $concat, $sql);
        } else {
            $sql = sprintf('WHERE %s', $sql);
        }

        $this->appendSql($sql);
    }

    /**
     * @param array $params
     */
    private function bindParams(array &$params)
    {
        foreach ($params as $key => &$param) {
            $this->bindParam($param);
        }
    }

    /**
     * @param mixed $param
     */
    private function bindParam(&$param)
    {
        if (is_numeric($param)) {
            $param = $param + 0;
        } else if (!empty($param)) {
            $param = sprintf("'%s'", stripcslashes($param));
        } else if (is_null($param)) {
            $param = 'null';
        } else {
            $param = "''";
        }
    }

    /**
     * @param array $values
     */
    public function update($values)
    {
        $this->bindParams($values);
        
        $params = array_map(function($key, $value) {
            return implode(' = ', [$key, $value]);
        }, array_keys($values), $values);

        $currentSql = $this->sql;
        $this->sql = sprintf('UPDATE `%s` SET %s', $this->table, implode(', ', $params));

        if (!empty($currentSql)) {
            $this->appendSql($currentSql);
        }
    }

    /**
     * Delete rows
     */
    public function delete()
    {
        $currentSql = $this->sql;
        $this->sql = sprintf('DELETE FROM `%s`', $this->table);

        if (!empty($currentSql)) {
            $this->appendSql($currentSql);
        }
    }

    /**
     * @param string $columns
     * @return self
     */
    public function select($columns = '*'): self
    {
        $this->sql = sprintf('SELECT %s FROM `%s`',
            $columns == '*' ? $columns : implode(', ', $columns), $this->table
        );

        return $this;
    }

    /**
     * @param array $data
     */
    public function insert(array $data)
    {
        $this->bindParams($data);

        if (array_key_exists('id', $data) && (empty($data['id']) || $data['id'] === "''")) {
            unset($data['id']);
        }

        $this->sql = sprintf('INSERT INTO `%s`(%s) VALUES(%s)',
            $this->table, implode(', ', array_keys($data)), implode(', ', array_values($data))
        );
    }

    /**
     * @param string|\Closure $column
     * @param string|null     $operatorOrValue
     * @param null            $value
     * @return self
     */
    public function where($column, string $operatorOrValue = null, $value = null): self
    {
        $this->addWhere($column, $operatorOrValue, $value, 'AND');
        return $this;
    }

    /**
     * @param string|\Closure $column
     * @param string|null     $operatorOrValue
     * @param null            $value
     * @return self
     */
    public function orWhere($column, string $operatorOrValue = null, $value = null): self
    {
        $this->addWhere($column, $operatorOrValue, $value, 'OR');
        return $this;
    }

    /**
     * @param string $sql
     */
    public function appendSql(string $sql)
    {
        $this->sql = trim($this->sql) . ' ' . $sql;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

}
