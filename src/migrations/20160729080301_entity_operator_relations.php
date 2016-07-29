<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE relations
(
-- Унаследована from table entities:  id bigint NOT NULL,
-- Унаследована from table entities:  created timestamp without time zone,
-- Унаследована from table entities:  changed timestamp without time zone,
-- Унаследована from table entities:  active boolean DEFAULT true,
  first bigint,
  second bigint,
  CONSTRAINT relations_pkey PRIMARY KEY (id)
)
INHERITS (entities);
SQL;
        $this->execute($sql);
    }
}
