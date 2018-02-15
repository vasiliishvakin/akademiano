<?php

use Phinx\Migration\AbstractMigration;

class TagsDictionariesRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tags_tags_dictionaries_relations
(
  PRIMARY KEY (id),
  FOREIGN KEY (first) REFERENCES tags_tags (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (second) REFERENCES tags_dictionaries (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (owner)  REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (tags_relations);
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Tags\Model\TagsDictionariesRelationWorker::TABLE_ID);
        $this->execute($sql);
    }
}
