<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorEntries extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE entries
(
  PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);


        $sql = "CREATE SEQUENCE uuid_complex_short_tables_3";
        $this->execute($sql);
    }
}
