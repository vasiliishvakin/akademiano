<?php

use Phinx\Migration\AbstractMigration;

class GroupsInitMysql extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table("groups")
            ->addColumn('name', 'string', array('limit' => 150))
            ->save();
        $this->execute('insert into groups (id, name) values (1, "user")');
        $users = $this->table('users')
            ->addColumn('group', 'integer', array('default' => 1, "null" => false))
            ->addForeignKey('group', 'groups', 'id', array('delete' => 'RESTRICT', 'update' => 'RESTRICT'))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}