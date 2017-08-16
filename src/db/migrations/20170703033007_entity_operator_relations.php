<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE relations
(
  first bigint NOT NULL,
  second bigint NOT NULL,
   FOREIGN KEY (first) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (second) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  PRIMARY KEY (id)
)
INHERITS (entities);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_4";
        $this->execute($sql);
    }
}
