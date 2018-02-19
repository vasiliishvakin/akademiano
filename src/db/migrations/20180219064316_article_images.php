<?php

use Phinx\Migration\AbstractMigration;

class ArticleImages extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE article_images
(
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (entity) REFERENCES articles (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (linked_images);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Articles\Model\ArticleImagesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
