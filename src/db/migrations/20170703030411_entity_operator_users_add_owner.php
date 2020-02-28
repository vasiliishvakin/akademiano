<?php

use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Worker\EntitiesWorker;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;
use Akademiano\EntityOperator\Worker\ContentEntitiesWorker;

class EntityOperatorUsersAddOwner extends AbstractMigration
{
    public function up()
    {
        $this->table(EntitiesWorker::TABLE_NAME)
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
        $this->table(NamedEntitiesWorker::TABLE_NAME)
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
        $this->table(ContentEntitiesWorker::TABLE_NAME)
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
    }
}
