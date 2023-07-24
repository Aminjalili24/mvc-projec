<?php

namespace System\Database\Traits;

trait HasSoftDelete
{

    public function deleteMethod($id = null)
    {
        $object = $this;
        if ($id) {
            $this->resetQuery();
            $object = $this->findMethod();
        }
        if ($object) {
            $object->resetQuery();


            $object->setSql("UPDATE " . $object->getTableName())." SET ".$this->getAttributeName($this->deletedAt)." = NOW() ";
            $object->setWehre('AND', $this->getAttributeName($object->primaryKey) . "= ?");
            $object->addValue($object->primaryKey, $object->{$object->primaryKey});
            return $object->executeQuery();
        }


    }
}