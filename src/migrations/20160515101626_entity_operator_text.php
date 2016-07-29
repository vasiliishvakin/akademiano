<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorText extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE texts
(
-- Унаследована from table named:  id bigint NOT NULL,
-- Унаследована from table named:  created timestamp without time zone,
-- Унаследована from table named:  changed timestamp without time zone,
-- Унаследована from table named:  active boolean DEFAULT true,
-- Унаследована from table named:  title text,
-- Унаследована from table named:  description text,
  content text,
  CONSTRAINT text_pkey PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);
    }
}
