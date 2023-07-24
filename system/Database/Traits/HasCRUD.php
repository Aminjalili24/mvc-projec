<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;


trait HasCRUD
{
    protected function createMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }

    protected function updateMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }

    public function deleteMethod($id)
    {
        $object = $this;
        $this->resetQuery();
        if ($id) {
            $object = $this->findMethod();
            $this->resetQuery();
        }
            $object->setSql("DELETE FROM " . $object->getTableName());
            $object->setWehre('AND', $this->getAttributeName($this->primaryKey) . "= ?");
            $object->addValue($object->primaryKey, $object->{$object->primaryKey});
            return $object->executeQuery();



    }


    protected function all()
    {
        $this->setSql('SELECT * FROM ' . $this->getTableName());
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];


    }

    protected function find($id)
    {
        $this->setSql("select * from  " . $this->getTableName());
        $this->setWehre('AND', $this->getAttributeName($this->primaryKey) . "= ?");
        $this->addValue($this->primaryKey, $id);
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        $this->setAllowedMethods(['update', 'delete', 'save']);
        if ($data) {
            return $this->arrayToAttributes($data);
        } else {
            return null;
        }


    }

    protected function whereMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {
            $condition = $this->getAttributeName($attribute) . ' =?';
            $this->addValue($attribute, $firstValue);
        } else {
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ?';
            $this->addValue($attribute, $secondValue);
        }
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;

    }

    protected function whereOrMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {
            $condition = $this->getAttributeName($attribute) . ' =?';
            $this->addValue($attribute, $firstValue);
        } else {
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ?';
            $this->addValue($attribute, $secondValue);
        }
        $operator = 'OR';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;

    }

    protected function whereNullMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {

            $condition = $this->getAttributeName($attribute) . ' IS NULL ';
            $operator = 'AND';
            $this->setWhere($operator, $condition);
            $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
            return $this;

        }
    }

    protected function whereNotNullMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {

            $condition = $this->getAttributeName($attribute) . ' IS NOT NULL ';
            $operator = 'AND';
            $this->setWhere($operator, $condition);
            $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
            return $this;

        }
    }

    protected function WhereInMethod($attribute, array $values)
    {
        if (is_array($values)) {
            $valuesArray = [];
            foreach ($values as $value) {
                $this->addValue($attribute, $value);
                $valuesArray[] = '?';
            }
            $condition = $this->getAttributeName($attribute) . ' IN (' . implode(' , ', $valuesArray) . ')';
            $operator = 'AND';
            $this->setWhere($operator, $condition);
            $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
            return $this;
        }
    }

    protected function orederBy($attribute, $expression)
    {

        $this->setOrderBy($attribute, $expression);
        $this->setAllowedMethods(['limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }


    protected function limitMethod($number, $from)
    {

        $this->setLimit($number, $from);

        $this->setAllowedMethods(['limit', 'get', 'paginate']);
        return $this;
    }

    protected function get($array = []){

        if($this->sql = ''){

            if(empty($array)){
                $fields = $this->getTableName() . '.*';
            }else{
                foreach ($array as $key => $field){
                    $array[$key] =$this->getAttributename($field);
                }
                $fields = implode(' ,  ',$array);
            }
            $this->setSql("SELECT $fields FROM".$this->gettableName());
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];


    }

    public function paginateMethod($perPage){
        $totalRows = $this->getCount();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1 ;
        $totalPages = ceil($totalRows/$perPage);
        $currentPage = min($currentPage, $totalPages);
        $currentPage = max($currentPage, 1);
        $currentRow = ($currentPage - 1) * $perPage;
        $this->setLimit($currentRow, $perPage);
        if($this->sql == ''){
            $this->setSql("SELECT ".$this->getTableName().".* FROM ".$this->getTableName());
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data){
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }





    protected function saveMethod()
    {
        $fillString = $this->fill();
        if (!isset($this->{$this->primaryKey})) {
            $this->setSql("INSERT INTO " . $this->getTableName() . " SET $fillString, " . $this->getAttributeName($this->createdAt) . "=Now()");
        } else {
            $this->setSql("UPDATE " . $this->getTableName() . " SET $fillString, " . $this->getAttributeName($this->updatedAt) . "=NOW()");
            $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . "= ?");
            $this->addValue($this->primaryKey, $this->{$this->primaryKey});

        }
        $this->executeQuery();
        $this->resetQuery();

        if (!isset($this->{$this->primaryKey})) {

            $object = $this->findMethod(DBConnection::newInsertId());
            $defaultVars = get_class_vars(get_called_class());
            $allVars = get_object_vars($object);
            $differentVars = array_diff(array_keys($allVars), array_keys($defaultVars));

            foreach ($defaultVars as $attribute) {
                $this->inCastsAttributes($attribute) === true ? $this->registerAttribute($this, $attribute, $this->castEncodeValue($attribute, $object->$attribute))
                    : $this->registerAttribute($this, $attribute, $object->$attribute);
            }

        }
        $this->resetQuery();
        $this->setAllowedMethods(['update', 'delete', 'find']);
        return $this;

    }


    protected function fill()
    {
        $fillArray = [];
        foreach ($this->fillable as $attribute) {
            if (isset($this->$attribute)) {
                // `users`.`email` = ?
                $fillArray[] = $this->getAttributeName($attribute) . ' = ?';
                $this->inCastsAttributes($attribute) === true ? $this->addValue($attribute, $this->castEncodeValue($attribute, $this->$attribute))
                    : $this->addValue($attribute, $this->$attribute);
                $fillString = implode(', ', $fillArray);
                return $fillString;
            }
        }
    }

}