<?php

use Phinx\Migration\AbstractMigration;

class AttachInitPg extends AbstractMigration
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
        $sql = <<<SQL
CREATE TABLE files
(
  id serial NOT NULL,
  section integer,
  object integer,
  type text,
  name text,
  description text,
  path text,
  CONSTRAINT files_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
SQL;
        $this->execute($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}