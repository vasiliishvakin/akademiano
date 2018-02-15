<?php

use Phinx\Migration\AbstractMigration;

class TagsRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tags_relations
(
  PRIMARY KEY (id),
  FOREIGN KEY (first) REFERENCES tags_tags (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (second) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (owner)
      REFERENCES public.users (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (relations);
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Tags\Model\TagsRelationsWorker::TABLE_ID);
        $this->execute($sql);
    }
}
