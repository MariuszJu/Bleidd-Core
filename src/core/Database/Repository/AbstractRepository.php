<?php

namespace Bleidd\Database\Repository;

use Bleidd\Application\Runtime;
use Bleidd\Database\Collection;
use Bleidd\Database\Adapter\PDO;
use Bleidd\Database\QueryBuilder;
use Bleidd\Database\AdapterInterface;
use Bleidd\Database\Events\EntitySaved;
use Bleidd\Database\Events\EntitySaving;
use Bleidd\Database\Events\QueryExecuted;
use Bleidd\Database\Entity\AbstractEntity;

abstract class AbstractRepository
{

    /** @var string */
    public $table;

    /** @var string */
    public $model;

    /** @var AdapterInterface */
    private $adapter;

    /** @var QueryBuilder */
    private $queryBuilder;

    /**
     * AbstractRepository constructor
     *
     * @param AdapterInterface|null $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if ($adapter) {
            $this->adapter = $adapter;
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        if (!$this->queryBuilder instanceof QueryBuilder) {
            $this->queryBuilder = new QueryBuilder($this->table);
        }

        return $this->queryBuilder;
    }

    /**
     * @return AdapterInterface
     */
    private function getAdapter(): AdapterInterface
    {
        if (!$this->adapter instanceof AdapterInterface) {
            $this->adapter = new PDO();
        }

        return $this->adapter;
    }

    /**
     * @param array $data
     * @return AbstractEntity
     */
    private function convert(array $data): AbstractEntity
    {
        /** @var $model AbstractEntity */
        $model = new $this->model;

        return $model->fill($data);
    }

    /**
     * @param array $array
     * @return Collection
     */
    private function convertArray(array $array): Collection
    {
        $collection = new Collection();
        
        foreach ($array as $element) {
            $collection->add($this->convert($element));
        }

        return $collection;
    }

    /**
     * @param string|\Closure $column
     * @param string|null     $operatorOrValue
     * @param null            $value
     * @param string          $concat
     * @return self
     */
    private function addWhere($column, string $operatorOrValue = null, $value = null, string $concat = 'AND'): self
    {
        $method = $concat == 'AND' ? 'where' : 'orWhere';

        if ($column instanceof \Closure) {
            $builder = new QueryBuilder($this->table);
            $column($builder);
            
            $sql = sprintf('%s (%s)', $concat, str_replace('WHERE ', '', $builder->getSql()));
            $this->getQueryBuilder()->appendSql($sql);
        } else {
            if (empty($value)) {
                $this->getQueryBuilder()->$method($column, '=', $operatorOrValue);
            } else {
                $this->getQueryBuilder()->$method($column, $operatorOrValue, $value);
            }
        }

        return $this;
    }
    
    /**
     * @param string $columns
     * @return self
     */
    public function fetch($columns = '*'): self
    {
        $this->getQueryBuilder()->select($columns);
        return $this;
    }

    /**
     * @param string|\\Closure $column
     * @param string|null      $operatorOrValue
     * @param null             $value
     * @return self
     */
    public function where($column, string $operatorOrValue = null, $value = null): self
    {
        $this->addWhere($column, $operatorOrValue, $value, 'AND');
        return $this;
    }

    /**
     * @param string|\\Closure $column
     * @param string|null      $operatorOrValue
     * @param null             $value
     * @return self
     */
    public function orWhere($column, string $operatorOrValue = null, $value = null): self
    {
        $this->addWhere($column, $operatorOrValue, $value, 'OR');
        return $this;
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        $sql = $this->getQueryBuilder()->getSql();

        if (strpos($sql, 'SELECT') === false) {
            $currentSql = $sql;
            $this->getQueryBuilder()->select();
            $this->getQueryBuilder()->appendSql($currentSql);
            $sql = $this->getQueryBuilder()->getSql();
        }

        $this->queryBuilder = null;
        
        $results = $this->getAdapter()->query($sql);
        Runtime::dispatcher()->fire(new QueryExecuted(QueryExecuted::TYPE_SELECT, $sql, $results));
        
        return $this->convertArray($results);
    }

    /**
     * @param mixed  $id
     * @param string $column
     * @return AbstractEntity|null
     */
    public function find($id, string $column = 'id')
    {
        return $this->fetch()
            ->where($column, $id)
            ->first();
    }

    /**
     * @param array $values
     * @return int
     */
    public function update(array $values): int
    {
        $this->getQueryBuilder()->update($values);
        $sql = $this->getQueryBuilder()->getSql();
        $this->queryBuilder = null;
        
        $result = $this->getAdapter()->execute($sql);
        Runtime::dispatcher()->fire(new QueryExecuted(QueryExecuted::TYPE_UPDATE, $sql));
        
        return $result;
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        $this->getQueryBuilder()->delete();
        $sql = $this->getQueryBuilder()->getSql();
        $this->queryBuilder = null;

        $result = $this->getAdapter()->execute($sql);
        Runtime::dispatcher()->fire(new QueryExecuted(QueryExecuted::TYPE_DELETE, $sql));
        
        return $result;
    }

    /**
     * @return AbstractEntity|null
     */
    public function first()
    {
        return $this->get()->first();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        $this->getQueryBuilder()->insert($data);
        $sql = $this->getQueryBuilder()->getSql();
        $this->queryBuilder = null;

        $result = $this->getAdapter()->execute($sql);
        Runtime::dispatcher()->fire(new QueryExecuted(QueryExecuted::TYPE_INSERT, $sql));
        
        return $result;
    }

    /**
     * @param AbstractEntity|array $entity
     * @return bool
     */
    public function save($entity): bool
    {
        if (is_array($entity)) {
            $data = $entity;
            $entity = new $this->model;
            $entity->fill($data);
        }

        Runtime::dispatcher()->fire(new EntitySaving($entity));
        
        if ($entity->isNew()) {
            if (!$this->insert($entity->toArray())) {
                return false;
            }
            
            $entity->id = $this->getAdapter()->lastInsertedId();
            Runtime::dispatcher()->fire(new EntitySaved($entity));
            
            return true;
        }
        
        if (!$this->where('id', $entity->id)
            ->update($entity->toArray())
        ) {
            return false;
        }
        
        Runtime::dispatcher()->fire(new EntitySaved($entity));
        
        return true;
    }

    /**
     * @return bool
     */
    public function truncate(): bool
    {
        return $this->getAdapter()->truncate($this->table);
    }

}
