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
        throw new \Exception("not use, Luke");
        $sql = <<<SQL
CREATE TABLE groups
(
  id serial NOT NULL,
  name character varying(150),
  created timestamp with time zone,
  CONSTRAINT users_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
CREATE UNIQUE INDEX name
  ON groups
  USING btree
  (name COLLATE pg_catalog."default");
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