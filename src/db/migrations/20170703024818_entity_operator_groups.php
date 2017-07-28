<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorGroups extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE groups
(
  CONSTRAINT groups_pkey PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);


        $sql = "CREATE SEQUENCE uuid_complex_short_tables_10";
        $this->execute($sql);
    }
}
