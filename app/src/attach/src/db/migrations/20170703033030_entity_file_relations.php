<?php

use Phinx\Migration\AbstractMigration;

class EntityFileRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE entity_file_relations
(
  first bigint NOT NULL,
  second bigint NOT NULL,
   FOREIGN KEY (first) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (second) REFERENCES files (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  PRIMARY KEY (id)
)
INHERITS (relations);
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Attach\Model\EntityFileRelationWorker::TABLE_ID);
        $this->execute($sql);
    }
}
