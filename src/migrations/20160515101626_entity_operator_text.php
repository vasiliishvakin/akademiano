<?php

use Phinx\Migration\AbstractMigration;

class DeltaPhp\OperatorNamed extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE public.texts
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
INHERITS (public.named);
SQL;
        $this->execute($sql);
    }
}
