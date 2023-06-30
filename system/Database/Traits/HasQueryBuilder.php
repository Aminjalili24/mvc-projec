<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;

trait HasQueryBuilder
{
    private $sql = '';
    protected $where = [];
    private $orderBy = [];
    private $limit = [];
    private $values = [];
    private $bindValues = [];


    protected function setSql($query)
    {
        $this->sql = $query;
    }

    protected function getSql()
    {
        return $this->sql;
    }

    protected function resetSql()
    {
        $this->sql = '';
    }

    protected function setWhere($operator, $condition)
    {

        $array = ['operator' => $operator, 'condition' => $condition];
        array_push($this->where, $array);

    }

    protected function resetWhere()
    {
        $this->where = [];
    }

    protected function setOrderBy($name, $expression)
    {
        $this->orderBy[] = $this->getAttributeName($name) . ' ' . $expression;
    }

    protected function resetOrderBy()
    {
        $this->orderBy = [];
    }

    protected function setLimit($from, $number)
    {
        $this->limit['from'] = (int)$from;
        $this->limit['number'] = (int)$number;
    }

    protected function resetLimit()
    {
        unset($this->limit['from'], $this->limit['number']);
    }

    protected function addValue($attribute, $value)
    {
        $this->values[$attribute] = $value;
        $this->bindValues[] = $value;
    }

    protected function removeValues()
    {
        $this->values = [];
        $this->bindValues = [];
    }

    protected function resetQuery()
    {
        $this->removeValues();
        $this->resetLimit();
        $this->resetOrderBy();
        $this->resetQuery();
        $this->resetSql();
        $this->resetWhere();
    }

    protected function executeQuery()
    {
        $query = '';
        $query .= $this->sql;
        if (!empty($this->where)) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString === '' ? $whereString .= $where['condition'] : $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $query .= ' WHERE ' . $whereString;
        }

        if (!empty($this->orderBy)) {

            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if (!empty($this->limit)) {

            $query .= ' limit ' . $this->limit['from'] . ' , ' . $this->limit['number'] . ' ';
        }
        $query .= ' ;';
        echo $query . '<hr>/';
        $pdoInstance = DBConnection::getDbConnectionInstance();
        $statement = $pdoInstance->prepare($query);
        if (count($this->bindValues) > count($this->values)) {
            count($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();


        } else {
            count($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
        }
        return $statement;
    }

    protected function getCount()
    {

        $query = '';
        $query .= "SELECT COUNT(".$this->getTableName().".*) FROM".$this->getTableName();

        $query .= ' ;';

        $pdoInstance = DBConnection::getDbConnectionInstance();
        $statement = $pdoInstance->prepare($query);
        if (count($this->bindValues) > count($this->values)) {
            count($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();


        } else {
            count($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
        }
        return $statement->fetchColumn();
    }

    protected function getTableName(){
        return ' `'.$this->table.'`';
    }
protected function getAttributeName($attribute){
        return ' `'.$this->table.'`.`'.$attribute.'` ';
    }

}











