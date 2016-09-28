<?php

use Phinx\Migration\AbstractMigration;

class EntityImageRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE public.entity_images_relation
(
-- Унаследована from table entity_file_relations:  id bigint NOT NULL,
-- Унаследована from table entity_file_relations:  created timestamp without time zone,
-- Унаследована from table entity_file_relations:  changed timestamp without time zone,
-- Унаследована from table entity_file_relations:  active boolean DEFAULT true,
-- Унаследована from table entity_file_relations:  first bigint,
-- Унаследована from table entity_file_relations:  second bigint,
  CONSTRAINT entity_images_relation_pkey PRIMARY KEY (id),
  CONSTRAINT entity_images_relation_first_fkey FOREIGN KEY (first)
      REFERENCES public.entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT entity_images_relation_second_fkey FOREIGN KEY (second)
      REFERENCES public.images (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.entity_file_relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_81";
        $this->execute($sql);
    }
}
