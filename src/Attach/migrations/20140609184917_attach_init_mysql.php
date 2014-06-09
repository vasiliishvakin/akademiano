<?php

use Phinx\Migration\AbstractMigration;

class AttachInitMysql extends AbstractMigration
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
        $this->table('files')
            ->addColumn('section', 'integer', array('default' => null, "null" => false))
            ->addColumn('object', 'integer', array('default' => null, "null" => false))
            ->addColumn('type', 'string', array('limit' => 150))
            ->addColumn('name', 'string', array('limit' => 150))
            ->addColumn('description', 'string', array('limit' => 250))
            ->addColumn('path', 'string', array('limit' => 250))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}