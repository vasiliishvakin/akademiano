<?php

use Phinx\Migration\AbstractMigration;

class ArticleImagesRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE article_images_relations
(
  CONSTRAINT article_files_relations_pkey PRIMARY KEY (id),
  CONSTRAINT page_files_relations_first_fkey FOREIGN KEY (first)
      REFERENCES articles (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT article_images_relations_second_fkey FOREIGN KEY (second)
      REFERENCES images (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_102";
        $this->execute($sql);
    }
}
