<?php

use Phinx\Migration\AbstractMigration;

class ImagesWithOperator extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE images
(
-- Унаследована from table files:  id bigint NOT NULL,
-- Унаследована from table files:  created timestamp without time zone,
-- Унаследована from table files:  changed timestamp without time zone,
-- Унаследована from table files:  active boolean DEFAULT true,
-- Унаследована from table files:  title text,
-- Унаследована from table files:  description text,
-- Унаследована from table files:  type text,
-- Унаследована from table files:  sub_type text,
-- Унаследована from table files:  path text,
  CONSTRAINT images_pkey PRIMARY KEY (id)
)
INHERITS (files);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_13";
        $this->execute($sql);
    }
}
