<?php

use Phinx\Migration\AbstractMigration;

class GroupsInitPg extends AbstractMigration
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
CREATE TABLE groups
(
  id serial NOT NULL,
  name character varying(150),
  created timestamp with time zone,
  CONSTRAINT groups_pkey PRIMARY KEY (id),
  CONSTRAINT groups_name_key UNIQUE (name)
);

ALTER TABLE users
  ADD COLUMN "group" integer;
ALTER TABLE users
  ADD FOREIGN KEY ("group") REFERENCES groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
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