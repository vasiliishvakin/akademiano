<?php

use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Worker\EntitiesWorker;

class EntityOperatorEntities extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  id bigint NOT NULL,
  created timestamp without time zone,
  changed timestamp without time zone,
  active boolean DEFAULT true,
  owner bigint,
  CONSTRAINT %s_pkey PRIMARY KEY (id)
);
SQL;
        $sql = sprintf($sql, EntitiesWorker::TABLE_NAME, EntitiesWorker::TABLE_NAME);
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(EntitiesWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
