<?php

namespace Bleidd\Database\Events;

use Bleidd\Event\AbstractEvent;

class QueryExecuted extends AbstractEvent
{
    
    const TYPE_SELECT = 'SELECT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_INSERT = 'INSERT';
    const TYPE_DELETE = 'DELETE';
    
    /** @var string */
    public static $name = 'SQL Query Executed';
    
    /** @var string */
    public $type;
    
    /** @var string */
    public $sql;
    
    /** @var array */
    public $results;
    
    /**
     * QueryExecuted constructor
     *
     * @param string $type
     * @param string $sql
     * @param mixed  $results
     */
    public function __construct(string $type, string $sql, $results = null)
    {
        $this->type = $type;
        $this->sql = $sql;
        $this->results = $results;
    }
    
}
