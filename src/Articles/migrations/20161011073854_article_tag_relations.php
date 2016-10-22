<?php

use Phinx\Migration\AbstractMigration;

class ArticleTagRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE article_tag_relations
(
-- Унаследована from table entity_tag_relations:  id bigint NOT NULL,
-- Унаследована from table entity_tag_relations:  created timestamp without time zone,
-- Унаследована from table entity_tag_relations:  changed timestamp without time zone,
-- Унаследована from table entity_tag_relations:  active boolean DEFAULT true,
-- Унаследована from table entity_tag_relations:  first bigint,
-- Унаследована from table entity_tag_relations:  second bigint,
  CONSTRAINT article_tag_relations_pkey PRIMARY KEY (id),
  CONSTRAINT article_tag_relations_first_fkey FOREIGN KEY (first)
      REFERENCES articles (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT article_tag_relations_second_fkey FOREIGN KEY (second)
      REFERENCES tags (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (entity_tag_relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_104";
        $this->execute($sql);
    }
}
