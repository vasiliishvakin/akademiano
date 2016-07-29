<?php

use Phinx\Migration\AbstractMigration;

class FilesWithOperator extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE files
(
-- Унаследована from table named:  id bigint NOT NULL,
-- Унаследована from table named:  created timestamp without time zone,
-- Унаследована from table named:  changed timestamp without time zone,
-- Унаследована from table named:  active boolean DEFAULT true,
-- Унаследована from table named:  title text,
-- Унаследована from table named:  description text,
  type text,
  sub_type text,
  path text,
  CONSTRAINT files_pkey PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);
        
        $sql = "CREATE SEQUENCE uuid_complex_short_tables_12";
        $this->execute($sql);
    }
}
