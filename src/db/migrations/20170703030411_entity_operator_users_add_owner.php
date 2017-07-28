<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorUsersAddOwner extends AbstractMigration
{
    public function up()
    {
        $this->table("entities")
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
        $this->table("named")
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
        $this->table("content")
            ->addForeignKey('owner', 'users', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
    }
}
