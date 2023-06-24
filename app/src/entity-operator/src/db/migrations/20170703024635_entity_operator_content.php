<?php

use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\EntityOperator\Worker\ContentEntitiesWorker;
use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class EntityOperatorContent extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  content text,
  CONSTRAINT %s_pkey PRIMARY KEY (id)
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql, ContentEntitiesWorker::TABLE_NAME, ContentEntitiesWorker::TABLE_NAME, NamedEntitiesWorker::TABLE_NAME);
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(ContentEntitiesWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
