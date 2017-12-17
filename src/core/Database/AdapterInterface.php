<?php

namespace Bleidd\Database;

interface AdapterInterface
{
    
    /**
     * @param string $sqlString
     * @return array
     */
    public function query(string $sqlString): array;
    
    /**
     * @param string $sqlString
     * @param array  $params
     * @return int
     */
    public function execute(string $sqlString, array $params = []): int;
    
    /**
     * @param string $table
     * @return bool
     */
    public function truncate(string $table): bool;
    
    /**
     * @return int
     */
    public function lastInsertedId(): int;

}
