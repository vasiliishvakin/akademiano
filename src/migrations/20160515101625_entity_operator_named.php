<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorNamed extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE named
(
-- Унаследована from table entities:  id bigint NOT NULL,
-- Унаследована from table entities:  created timestamp without time zone,
-- Унаследована from table entities:  changed timestamp without time zone,
-- Унаследована from table entities:  active boolean DEFAULT true,
  title text,
  description text,
  CONSTRAINT named_pkey PRIMARY KEY (id)
)
INHERITS (entities);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_2";
        $this->execute($sql);
    }
}
