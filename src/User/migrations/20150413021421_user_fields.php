<?php

use Phinx\Migration\AbstractMigration;

class UserFields extends AbstractMigration
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

        $users = $this->table('users');

        $users->addColumn('first_name', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('last_name', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('confirmed', 'boolean', ['default' => false, 'null' => false])
            ->addColumn('changed', 'timestamp', ['timezone' => true, 'null' => true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}