<?php

use Phinx\Migration\AbstractMigration;

class CommentFiles extends AbstractMigration
{

    public function up()
    {
        $sql = <<<SQL
CREATE TABLE comment_files
(
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (entity) REFERENCES comments (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (linked_files);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Comments\Model\CommentFilesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
