<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorComments extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE comments
(
   entity bigint, 
   FOREIGN KEY (entity) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT, 
   FOREIGN KEY (owner) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
   PRIMARY KEY (id)
)
INHERITS (content);
SQL;
        $this->execute($sql);

        $tableId = \Akademiano\Content\Comments\Model\CommentsWorker::TABLE_ID;

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_{$tableId}";
        $this->execute($sql);
    }
}
