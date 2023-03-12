<?php

namespace duxet\Rethinkdb;

use duxet\Rethinkdb\Query\Builder as QueryBuilder;
use duxet\Rethinkdb\Schema\Grammar;
use r;

class Connection extends \Illuminate\Database\Connection
{
    /**
     * The RethinkDB connection handler.
     *
     * @var r\Connection
     */
    protected $connection;

    /**
     * Create a new database connection instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->database = $config['database'];

        $dbConf = [
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'db' => $config['database'] ?? null,
            'user' => $config['username'] ?? null,
            'password' => $config['password'] ?? null,
        ];

        // Create the connection
        $this->connection = r\connect($dbConf);

        // We need to initialize a query grammar and the query post processors,
        // which are both very important parts of the database abstractions -
        // so we initialize these to their default values when starting.
        $this->useDefaultQueryGrammar();
        $this->useDefaultPostProcessor();
        $this->schemaGrammar = new Grammar();
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param string $table
     * @param null|string $as
     * 
     * @return QueryBuilder
     */
    public function table($table, $as = null)
    {
        $query = new QueryBuilder($this);

        return $query->from($table);
    }

    /**
     * Get a RethinkDB connection.
     *
     * @return \r\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param int $start
     *
     * @return float
     */
    public function getElapsedTime($start)
    {
        return parent::getElapsedTime($start);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return Schema\Builder
     */
    public function getSchemaBuilder()
    {
        return new Schema\Builder($this);
    }
}
