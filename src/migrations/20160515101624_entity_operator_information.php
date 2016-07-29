<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorInformation extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE information
(
-- Унаследована from table entities:  id bigint NOT NULL,
-- Унаследована from table entities:  created timestamp without time zone,
-- Унаследована from table entities:  changed timestamp without time zone,
-- Унаследована from table entities:  active boolean DEFAULT true,
  title text,
  description text,
  content text,
  CONSTRAINT information_pkey PRIMARY KEY (id)
)
INHERITS (entities);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_2";
        $this->execute($sql);
    }
}
