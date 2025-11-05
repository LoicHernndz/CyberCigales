<?php

namespace Tests\Mocks;

class MockDatabase
{
    public array $queries = [];
    public array $params = [];
    public $returnValue = true;
    public $singleReturn = null;
    public $resultSetReturn = [];

    public function query($sql)
    {
        $this->queries[] = $sql;
    }

    public function bind($param, $value)
    {
        $this->params[$param] = $value;
    }

    public function execute()
    {
        return $this->returnValue;
    }

    public function single()
    {
        return $this->singleReturn;
    }

    public function resultSet()
    {
        return $this->resultSetReturn;
    }

    public function rowCount()
    {
        return 1;
    }
}

