<?php

use Phinx\Migration\AbstractMigration;

class Countries extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE countries
(
  PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Countries\Model\CountriesWorker::TABLE_ID);
        $this->execute($sql);

    }
}
