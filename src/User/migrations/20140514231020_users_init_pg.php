<?php

use Phinx\Migration\AbstractMigration;

class UsersInitPg extends AbstractMigration
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
CREATE TABLE users
(
  id serial NOT NULL,
  email character varying(150),
  password character varying(150),
  created timestamp with time zone,
  CONSTRAINT users_pkey PRIMARY KEY (id)
);
CREATE UNIQUE INDEX email
  ON users
  USING btree
  (email COLLATE pg_catalog."default");
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