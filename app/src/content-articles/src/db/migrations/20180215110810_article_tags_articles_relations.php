<?php

use Phinx\Migration\AbstractMigration;

class ArticleTagsArticlesRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE article_tags_articles_relations
(
  PRIMARY KEY (id),
  FOREIGN KEY (first) REFERENCES tags_tags (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (second) REFERENCES articles (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (owner)  REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (tags_relations);
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Articles\Model\TagsArticlesRelationsWorker::TABLE_ID);
        $this->execute($sql);
    }
}
