<?php

use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;
use \Akademiano\EntityOperator\Worker\EntitiesWorker;

class EntityOperatorNamed extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
-- Унаследована from table entities:  id bigint NOT NULL,
-- Унаследована from table entities:  created timestamp without time zone,
-- Унаследована from table entities:  changed timestamp without time zone,
-- Унаследована from table entities:  active boolean DEFAULT true,
  title text,
  description text,
  CONSTRAINT %s_pkey PRIMARY KEY (id)
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql, NamedEntitiesWorker::TABLE_NAME, NamedEntitiesWorker::TABLE_NAME, EntitiesWorker::TABLE_NAME);
        $this->execute($sql);
        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(NamedEntitiesWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
