<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorUsers extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE users
(
  email text,
  password text,
  "group" bigint,
  CONSTRAINT users_pkey PRIMARY KEY (id),
  CONSTRAINT email UNIQUE (email),
  CONSTRAINT users_group_fkey FOREIGN KEY ("group")
      REFERENCES public.groups (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (named);
SQL;
        $this->execute($sql);


        $sql = "CREATE SEQUENCE uuid_complex_short_tables_11";
        $this->execute($sql);
    }
}
