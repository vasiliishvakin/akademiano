<?php

use Phinx\Migration\AbstractMigration;

class EntityFileRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE public.entity_file_relations
(
-- Унаследована from table relations:  id bigint NOT NULL,
-- Унаследована from table relations:  created timestamp without time zone,
-- Унаследована from table relations:  changed timestamp without time zone,
-- Унаследована from table relations:  active boolean DEFAULT true,
-- Унаследована from table relations:  first bigint,
-- Унаследована from table relations:  second bigint,
  CONSTRAINT entity_file_relations_pkey PRIMARY KEY (id),
  CONSTRAINT entity_file_relations_first_fkey FOREIGN KEY (first)
      REFERENCES public.entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT entity_file_relations_second_fkey FOREIGN KEY (second)
      REFERENCES public.files (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_80";
        $this->execute($sql);
    }
}
