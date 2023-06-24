<?php

use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\EntityOperator\Worker\RelationsWorker;
use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Worker\EntitiesWorker;

class EntityOperatorRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  first bigint NOT NULL,
  second bigint NOT NULL,
   FOREIGN KEY (first) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (second) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  PRIMARY KEY (id)
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql,
            RelationsWorker::TABLE_NAME,
            EntitiesWorker::TABLE_NAME,
            EntitiesWorker::TABLE_NAME,
            EntitiesWorker::TABLE_NAME
        );
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(RelationsWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
